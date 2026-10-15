<?php

namespace app\service\controller;
use app\service\Base;
use app\model\controller\Permission as PermissionModel;

class Permission extends Base
{
	/**
	 * 请求级内存缓存，避免同一次请求重复查询数据库
	 * @var array
	 */
	protected static $userPermCache = [];

	/**
	 * 控制器方法元数据缓存
	 * @var array|null
	 */
	protected static $catalogCache = null;

	/**
	 * 判断是否为超级管理员
	 */
	public function isSuperAdmin(int $memId = 0): bool
	{
		if ($memId <= 0) {
			$memId = (int)userId();
		}
		return $memId === PermissionModel::SUPER_ADMIN_ID;
	}

	/**
	 * 清理内存权限缓存
	 */
	public function clearCache(): void
	{
		self::$userPermCache = [];
	}

	/**
	 * 获取指定用户生效的权限映射表（部门权限 U 个人权限）
	 * 返回结构: ['product' => ['*' => true, 'index' => true], ...] (键名全小写便于忽略大小写匹配，同时保留 _raw_actions 供跳转使用)
	 */
	public function getUserEffectivePermMap(int $memId = 0): array
	{
		if ($memId <= 0) {
			$memId = (int)userId();
		}
		if ($memId <= 0) {
			return [];
		}
		if (isset(self::$userPermCache[$memId])) {
			return self::$userPermCache[$memId];
		}
		if ($this->isSuperAdmin($memId)) {
			return self::$userPermCache[$memId] = ['*' => ['*' => true]];
		}

		// 查询人员所属部门
		$rel = service('member/MemberDepartment')->getByMemId($memId);
		$deptId = (int)($rel['dept_id'] ?? 0);

		if ($deptId > 0) {
			$sql = sprintf(
				'SELECT controller, action FROM %s WHERE (target_type = %d AND target_id = %d) OR (target_type = %d AND target_id = %d)',
				$this->getTable(),
				PermissionModel::TYPE_DEPARTMENT,
				$deptId,
				PermissionModel::TYPE_MEMBER,
				$memId
			);
			$rows = $this->getQuery($sql);
		} else {
			$rows = $this->getListData([
				'target_type' => PermissionModel::TYPE_MEMBER,
				'target_id'   => $memId,
			], 'controller,action');
		}

		$map = [];
		foreach ($rows as $row) {
			$ctrl = strtolower(trim((string)($row['controller'] ?? '')));
			$actRaw = trim((string)($row['action'] ?? ''));
			$act = strtolower($actRaw);
			if ($ctrl === '' || $act === '') {
				continue;
			}
			$map[$ctrl][$act] = $actRaw;
		}

		return self::$userPermCache[$memId] = $map;
	}

	/**
	 * 校验用户是否有某个 控制器/public方法 的访问权限
	 */
	public function hasAccess(string $controller, string $action = 'index', int $memId = 0): bool
	{
		if ($memId <= 0) {
			$memId = (int)userId();
		}
		if ($memId <= 0) {
			return false;
		}

		$ctrlLower = strtolower(trim($controller));
		$actLower = strtolower(trim($action !== '' ? $action : 'index'));

		// 公共免检控制器与后台基础外框首页(Index/index)
		if (in_array($ctrlLower, ['login', 'api', 'pagenotfound'], true)) {
			return true;
		}
		if ($ctrlLower === 'index' && $actLower === 'index') {
			return true;
		}

		if ($this->isSuperAdmin($memId)) {
			return true;
		}

		$map = $this->getUserEffectivePermMap($memId);
		if (!empty($map['*']['*'])) {
			return true;
		}
		if (!empty($map[$ctrlLower]['*'])) {
			return true;
		}
		return isset($map[$ctrlLower][$actLower]);
	}

	/**
	 * 过滤左侧导航菜单（无权限的模块自动隐藏；若仅授权了非 index 的 public 方法则自动修正入口链接）
	 */
	public function filterMenuList(array $funcList, int $memId = 0): array
	{
		if ($memId <= 0) {
			$memId = (int)userId();
		}
		if ($this->isSuperAdmin($memId)) {
			return $funcList;
		}

		$map = $this->getUserEffectivePermMap($memId);
		if (!empty($map['*']['*'])) {
			return $funcList;
		}

		$filteredGroups = [];
		foreach ($funcList as $group) {
			if (empty($group['son']) || !is_array($group['son'])) {
				continue;
			}
			$allowedSons = [];
			foreach ($group['son'] as $sv) {
				$val = trim((string)($sv['value'] ?? ''), '/');
				if ($val === '') {
					continue;
				}
				if (strpos($val, '/') !== false) {
					[$c, $a] = explode('/', $val, 2);
					if ($this->hasAccess($c, $a, $memId)) {
						$allowedSons[] = $sv;
					}
				} else {
					$ctrlLower = strtolower($val);
					if ($this->hasAccess($val, 'index', $memId)) {
						$allowedSons[] = $sv;
					} elseif (!empty($map[$ctrlLower])) {
						// 用户没有 index 权限，但拥有该控制器下其他 public 方法权限，将菜单链接指向第一个有权限的方法
						$firstAct = reset($map[$ctrlLower]);
						if ($firstAct && $firstAct !== '*') {
							$sv['value'] = $val . '/' . $firstAct;
						}
						$allowedSons[] = $sv;
					}
				}
			}
			if (!empty($allowedSons)) {
				$group['son'] = $allowedSons;
				$filteredGroups[] = $group;
			}
		}

		return $filteredGroups;
	}

	/**
	 * 过滤控制器顶部标签栏 (_arr)
	 */
	public function filterControllerTabs(string $controller, array $tabs, int $memId = 0): array
	{
		if (empty($tabs) || $this->isSuperAdmin($memId)) {
			return $tabs;
		}
		$filtered = [];
		foreach ($tabs as $func => $label) {
			if ($this->hasAccess($controller, (string)$func, $memId)) {
				$filtered[$func] = $label;
			}
		}
		return $filtered;
	}

	/**
	 * 获取指定对象（部门或个人）已配置的权限列表
	 */
	public function getTargetPermList(int $targetType, int $targetId): array
	{
		if ($targetId <= 0) {
			return [];
		}
		return $this->getListData([
			'target_type' => $targetType,
			'target_id'   => $targetId,
		], 'controller,action');
	}

	/**
	 * 保存指定对象（部门或个人）的权限配置
	 * @param int $targetType 1=部门, 2=个人
	 * @param int $targetId   dept_id 或 mem_id
	 * @param array $rules    [['controller' => 'Product', 'action' => '*'], ...]
	 */
	public function saveTargetPermissions(int $targetType, int $targetId, array $rules): bool
	{
		if (!in_array($targetType, [PermissionModel::TYPE_DEPARTMENT, PermissionModel::TYPE_MEMBER], true) || $targetId <= 0) {
			return false;
		}

		// 整理去重：如果某个控制器勾选了 '*'（整个控制器），则仅保留 '*' 一条记录，减少数据表体积
		$organized = [];
		foreach ($rules as $rule) {
			$ctrl = ucfirst(trim((string)($rule['controller'] ?? '')));
			$act = trim((string)($rule['action'] ?? ''));
			if ($ctrl === '' || $act === '') {
				continue;
			}
			if ($act === '*') {
				$organized[$ctrl] = ['*' => true];
			} else {
				if (!isset($organized[$ctrl]['*'])) {
					$organized[$ctrl][$act] = true;
				}
			}
		}

		$now = time();
		$insertRows = [];
		foreach ($organized as $ctrl => $actions) {
			foreach ($actions as $act => $_) {
				$insertRows[] = [
					'target_type' => $targetType,
					'target_id'   => $targetId,
					'controller'  => $ctrl,
					'action'      => (string)$act,
					'add_time'    => $now,
				];
			}
		}

		// 先清空该对象原有权限，再批量写入
		$this->deleteData([
			'target_type' => $targetType,
			'target_id'   => $targetId,
		]);

		if (!empty($insertRows)) {
			$this->insert($insertRows);
		}

		$this->clearCache();
		return true;
	}

	/**
	 * 按 target_type 统计各对象的权限规则条数 [target_id => count]
	 */
	public function getCountMapByType(int $targetType): array
	{
		$rows = $this->getListData(['target_type' => $targetType], 'target_id,COUNT(*) as cnt', 0, 0, [], 'target_id');
		if (empty($rows)) {
			return [];
		}
		return array_column($rows, 'cnt', 'target_id');
	}

	/**
	 * 扫描并组装后台所有可授权控制器及其 public 方法目录（按功能分组归类）
	 */
	public function getControllerMethodCatalog(): array
	{
		if (self::$catalogCache !== null) {
			return self::$catalogCache;
		}

		// 1. 扫描 app/controller/admin/*.php 获取所有控制器的 public 方法与中文标题
		$skipControllers = ['Login', 'Api', 'PageNotFound'];
		$controllerMeta = [];
		$files = glob(ROOT_PATH . 'app/controller/admin/*.php') ?: [];

		foreach ($files as $file) {
			$ctrlName = basename($file, '.php');
			if (in_array($ctrlName, $skipControllers, true)) {
				continue;
			}
			$meta = $this->parseControllerFile($file, $ctrlName);
			if (!empty($meta['methods'])) {
				$controllerMeta[strtolower($ctrlName)] = $meta;
			}
		}

		// 2. 结合 controller 表的分组结构组织展示
		$menuTree = service('controller/Controller')->getList();
		$assignedCtrlKeys = [];
		$groups = [];

		foreach ($menuTree as $group) {
			$groupItems = [];
			foreach ($group['son'] ?? [] as $son) {
				$val = trim((string)($son['value'] ?? ''), '/');
				if ($val === '') {
					continue;
				}
				$ctrlPart = strpos($val, '/') !== false ? strstr($val, '/', true) : $val;
				$ctrlKey = strtolower($ctrlPart);
				if (isset($assignedCtrlKeys[$ctrlKey]) || !isset($controllerMeta[$ctrlKey])) {
					continue;
				}
				$assignedCtrlKeys[$ctrlKey] = true;
				$item = $controllerMeta[$ctrlKey];
				$item['menu_name'] = $son['name'];
				$groupItems[] = $item;
			}
			if (!empty($groupItems)) {
				$groups[] = [
					'group_id'   => (int)$group['con_id'],
					'group_name' => $group['name'],
					'items'      => $groupItems,
				];
			}
		}

		// 3. 将未挂载到菜单表的其他控制器归入“其他模块”
		$otherItems = [];
		foreach ($controllerMeta as $ctrlKey => $meta) {
			if (!isset($assignedCtrlKeys[$ctrlKey])) {
				$meta['menu_name'] = $meta['default_title'] ?: $meta['controller'];
				$otherItems[] = $meta;
			}
		}
		if (!empty($otherItems)) {
			$groups[] = [
				'group_id'   => 0,
				'group_name' => '其他模块',
				'items'      => $otherItems,
			];
		}

		return self::$catalogCache = $groups;
	}

	/**
	 * 解析单个控制器文件的 _default、_arr 以及 public 方法列表
	 */
	protected function parseControllerFile(string $file, string $ctrlName): array
	{
		$content = file_get_contents($file) ?: '';

		// 提取 $_default 名称
		$defaultTitle = '';
		if (preg_match('/\$this->_default\s*=\s*[\'"]([^\'"]*)[\'"]/', $content, $defMatch)) {
			$defaultTitle = trim($defMatch[1]);
		}

		// 提取 $_arr 映射 [method => title]
		$arrMap = [];
		if (preg_match('/\$this->_arr\s*=\s*\[(.*?)\];/s', $content, $arrMatch)) {
			if (preg_match_all('/[\'"]([a-zA-Z0-9_]+)[\'"]\s*=>\s*[\'"]([^\'"]*)[\'"]/', $arrMatch[1], $pairs, PREG_SET_ORDER)) {
				foreach ($pairs as $p) {
					$arrMap[$p[1]] = $p[2];
				}
			}
		}

		// 使用反射提取当前控制器自身声明的 public 实例方法
		$fqcn = 'app\\controller\\admin\\' . $ctrlName;
		$methods = [];
		if (class_exists($fqcn)) {
			$ref = new \ReflectionClass($fqcn);
			foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $m) {
				if ($m->getDeclaringClass()->getName() !== $ref->getName()) {
					continue;
				}
				$mName = $m->getName();
				if ($m->isConstructor() || $m->isStatic() || strpos($mName, '_') === 0) {
					continue;
				}
				// Index::index 为后台主框架壳，默认对所有已登录管理员开放，无需单独勾选
				if ($ctrlName === 'Index' && $mName === 'index') {
					continue;
				}
				$methods[] = [
					'action' => $mName,
					'title'  => $arrMap[$mName] ?? ($mName === 'index' ? ($defaultTitle ?: '列表页') : $mName),
				];
			}
		}

		return [
			'controller'    => $ctrlName,
			'default_title' => $defaultTitle,
			'methods'       => $methods,
		];
	}
}
