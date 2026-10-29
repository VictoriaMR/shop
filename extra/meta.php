<?php
/**
 * 自动扫描并生成 .phpstorm.meta.php 元数据文件
 * 支持 PhpStorm 与 VS Code (Intelephense)
 *
 * 使用方法：php extra/meta.php
 */

define('ROOT_PATH', dirname(__DIR__) . '/');

function scanClasses($dir, $prefix) {
    $res = [];
    if (!is_dir($dir)) {
        return $res;
    }
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = str_replace('\\', '/', $file->getPathname());
            $stripClean = str_replace('\\', '/', $dir);
            $rel = substr($path, strlen($stripClean));
            $rel = preg_replace('/\.php$/', '', $rel);
            $key = ltrim($rel, '/');
            $class = '\\' . $prefix . '\\' . str_replace('/', '\\', $key);
            $res[$key] = $class;
        }
    }
    ksort($res);
    return $res;
}

$frameMap = [
    'App'        => '\App',
    'Connection' => '\frame\Connection',
    'Cookie'     => '\frame\Cookie',
    'Debug'      => '\frame\Debug',
    'Error'      => '\frame\Error',
    'Html'       => '\frame\Html',
    'Http'       => '\frame\Http',
    'IP'         => '\frame\IP',
    'Locker'     => '\frame\Locker',
    'Paginator'  => '\frame\Paginator',
    'Query'      => '\frame\Query',
    'Redis'      => '\frame\Redis',
    'Request'    => '\frame\Request',
    'Router'     => '\frame\Router',
    'Session'    => '\frame\Session',
    'Task'       => '\frame\Task',
    'Time'       => '\frame\Time',
    'View'       => '\frame\View',
];

$services = scanClasses(ROOT_PATH . 'app/service', 'app\service');
// 兼容代码中的简写调用
$services['Image']     = '\app\service\tool\Image';
$services['Translate'] = '\app\service\tool\Translate';
$services['Socket']    = '\app\service\tool\Socket';
$services['File']      = '\app\service\tool\File';
$services['IP']        = '\app\service\tool\IP';
$services['Queue']     = '\app\service\tool\Queue';
$services['Sort']      = '\app\service\tool\Sort';
$services['Widget']    = '\app\service\tool\Widget';
$services['Language']  = '\app\service\system\Language';
$services['Currency']  = '\app\service\system\Currency';
ksort($services);

$models = scanClasses(ROOT_PATH . 'app/model', 'app\model');

$code = "<?php\n";
$code .= "/**\n";
$code .= " * PhpStorm & VS Code (Intelephense) Meta File\n";
$code .= " *\n";
$code .= " * 为容器工厂函数 frame()、service()、model() 等提供静态类型映射、代码自动补全及 Ctrl+点击 跳转支持。\n";
$code .= " */\n\n";
$code .= "namespace PHPSTORM_META {\n\n";

// 1. frame
$code .= "    // 1. frame(...) 基础框架组件映射\n";
$code .= "    override(\\frame(0), map([\n";
foreach ($frameMap as $key => $cls) {
    $code .= "        " . var_export($key, true) . " => {$cls}::class,\n";
}
$code .= "    ]));\n\n";

// 2. make & App::make
$code .= "    // 2. 通用 make(...) 与 \\App::make(...) 映射\n";
$code .= "    override(\\make(0), map([\n";
$code .= "        '' => '@',\n";
$code .= "    ]));\n\n";
$code .= "    override(\\App::make(0), map([\n";
$code .= "        '' => '@',\n";
$code .= "    ]));\n\n";

// 3. service
$code .= "    // 3. service(...) 业务服务层映射 (" . count($services) . " 个服务组件)\n";
$code .= "    override(\\service(0), map([\n";
foreach ($services as $key => $cls) {
    $code .= "        " . var_export($key, true) . " => {$cls}::class,\n";
}
$code .= "    ]));\n\n";

// 4. model
$code .= "    // 4. model(...) 数据模型层映射 (" . count($models) . " 个模型组件)\n";
$code .= "    override(\\model(0), map([\n";
foreach ($models as $key => $cls) {
    $code .= "        " . var_export($key, true) . " => {$cls}::class,\n";
}
$code .= "    ]));\n";
$code .= "}\n";

$target = ROOT_PATH . '.phpstorm.meta.php';
file_put_contents($target, $code);
echo "Successfully updated " . $target . " with " . count($services) . " services and " . count($models) . " models.\n";
