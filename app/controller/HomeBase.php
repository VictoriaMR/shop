<?php

namespace app\controller;

class HomeBase extends Base
{
	public function __construct()
	{
		$this->trace((bool)iget('_trace', 0));
		$this->handleLocaleSwitch();
		if (!isAjax() && \App::get('router', 'path') !== 'Api') {
			$this->initCommonViewData();
		}
		$template = \App::get('domain', 'template', 'template1');
		$class = 'template/' . $template . '/controller/' . (isMobile() ? 'mobile/' : 'computer/') . \App::get('router', 'path');
		if (is_file(ROOT_PATH . $class . '.php')) {
			$tplCtrl = \App::make($class);
			$func = (string)\App::get('router', 'func');
			if ($func !== '' && is_callable([$tplCtrl, $func])) {
				$tplCtrl->$func();
			}
		}
	}

	protected function isLogin()
	{
		return userId() > 0;
	}

	protected function handleLocaleSwitch()
	{
		$cur = strtoupper(trim((string)iget('cur', '')));
		if ($cur !== '') {
			$curInfo = service('system/Currency')->getInfoCache($cur);
			if (!empty($curInfo['code'])) {
				frame('Session')->set('site_currency_code', $curInfo['code']);
				frame('Cookie')->updateCurrency(strtolower($curInfo['code']));
			}
		}
		$lan = strtolower(trim((string)iget('lan', '')));
		if ($lan !== '') {
			$lanInfo = service('system/Language')->getInfoCache($lan);
			if (!empty($lanInfo['code'])) {
				frame('Session')->set('site_language_code', $lanInfo['code']);
				frame('Session')->set('site_language_id', (int)$lanInfo['lan_id']);
				frame('Cookie')->updateLanguage($lanInfo['code']);
			}
		}
	}

	protected function initCommonViewData()
	{
		$siteName = \App::get('domain', 'name', 'Shop');
		$siteInfo = service('site/Site')->getInfoCache(siteId());
		$siteEmail = !empty($siteInfo['email']) ? $siteInfo['email'] : ('support@' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $siteName) ?: 'shop') . '.com');

		// 币种列表与当前币种
		$currencyList = service('site/CurrencyUsed')->getListCache(siteId());
		if (empty($currencyList)) {
			$defaultCurrencies = ['USD', 'EUR', 'GBP', 'CAD', 'AUD'];
			$curService = service('system/Currency');
			foreach ($defaultCurrencies as $code) {
				$cInfo = $curService->getInfoCache($code);
				if (!empty($cInfo)) {
					$currencyList[] = $cInfo;
				}
			}
		}
		$curCurrencyCode = currencyId('code');
		$curCurrencyInfo = service('system/Currency')->getInfoCache($curCurrencyCode);
		$curCurrencySymbol = $curCurrencyInfo['symbol'] ?? '$';

		// 语言列表与当前语言
		$languageList = service('site/LanguageUsed')->getListCache(siteId());
		if (empty($languageList)) {
			$defaultLanguages = ['en', 'de', 'fr', 'es', 'jp'];
			$lanService = service('system/Language');
			foreach ($defaultLanguages as $code) {
				$lInfo = $lanService->getInfoCache($code);
				if (!empty($lInfo)) {
					$languageList[] = $lInfo;
				}
			}
		}
		$curLanCode = lanId('code');
		$curLanInfo = service('system/Language')->getInfoCache($curLanCode);
		$curLanName = $curLanInfo['name'] ?? 'English';

		// 顶部导航分类树（最多展示8个核心一级分类及其二级子分类）
		$navCategories = $this->getNavCategories();

		// 热门搜索词
		$hotKeywords = ['Dress', 'Sweater', 'T-Shirt', 'Chandelier', 'Ceiling Lamp', 'Nordic'];

		$this->assign('siteName', $siteName);
		$this->assign('siteEmail', $siteEmail);
		$this->assign('currencyList', $currencyList);
		$this->assign('curCurrencyCode', $curCurrencyCode);
		$this->assign('curCurrencySymbol', $curCurrencySymbol);
		$this->assign('languageList', $languageList);
		$this->assign('curLanCode', $curLanCode);
		$this->assign('curLanName', $curLanName);
		$this->assign('navCategories', $navCategories);
		$this->assign('hotKeywords', $hotKeywords);
	}

	protected function getNavCategories(): array
	{
		$cateService = service('category/Category');
		$enMap = $cateService->getEnMap();
		$allList = $cateService->getListFormat();
		if (empty($allList)) {
			return [];
		}

		$preferredRoots = [112, 111, 110, 101, 102, 104, 105, 109];
		$roots = [];
		$childrenMap = [];

		foreach ($allList as $row) {
			if (empty($row['status']) || empty($row['is_show'])) {
				continue;
			}
			$cid = (int)$row['cate_id'];
			$pid = (int)$row['parent_id'];
			$displayName = $enMap[$cid] ?? $row['name'];
			$item = [
				'cate_id' => $cid,
				'name'    => $displayName,
				'url'     => url('category', ['cid' => $cid, 'name' => $displayName]),
			];
			if ($pid === 0) {
				$roots[$cid] = $item;
			} elseif (isset($roots[$pid]) && count($childrenMap[$pid] ?? []) < 6) {
				$childrenMap[$pid][] = $item;
			}
		}

		$nav = [];
		foreach ($preferredRoots as $rootId) {
			if (isset($roots[$rootId])) {
				$rootItem = $roots[$rootId];
				$rootItem['children'] = $childrenMap[$rootId] ?? [];
				$nav[] = $rootItem;
			}
		}
		return $nav;
	}
}