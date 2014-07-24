<?php

// define globals
global $SITE, $DOCS_ROOT, $BASE_URL, $BASE_ROUTE, $CONFIG, $RAPIDO_DIR;

define('RAPIDO_TIME_START'  , microtime(true));
define('RAPIDO_MEMORY_START', memory_get_usage());

// autoload from vendor (PSR-0)
spl_autoload_register(function($class){
    $class_path = __DIR__.'/vendor/'.str_replace('\\', '/', $class).'.php';
    if(file_exists($class_path)) include_once($class_path);
});


// configuration

$RAPIDO_DIR = str_replace(DIRECTORY_SEPARATOR, '/', __DIR__);
$DOCS_ROOT  = str_replace(DIRECTORY_SEPARATOR, '/', isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : __DIR__);

$BASE       = trim(str_replace($DOCS_ROOT, '', $RAPIDO_DIR), "/");
$BASE_URL   = strlen($BASE) ? "/{$BASE}": $BASE;
$BASE_ROUTE = file_exists(__DIR__.'/.htaccess') ? $BASE_URL : "{$BASE_URL}/index.php";

$CONFIG     = array_merge([
    "app.name"          => "Rapido",
    "version"           => "0.0.1",
    "session.name"      => "rapidosession",
    "sec-key"           => "c32h4c4c-db44-s5h7-a814-bd5g1a15e5e1",
    "timezone"          => "UTC",
    "base_url"          => $BASE_URL,
    "base_route"        => $BASE_ROUTE,
    "docs_root"         => $DOCS_ROOT,
    "theme"             => "default",

    "site.meta"    => [
        "title"    => "Rapido", // default page title
        "layout"   => "theme:theme.php",
        "scripts"  => []
    ]

], include(__DIR__.'/config.php'));

// set default timezone
date_default_timezone_set($CONFIG['timezone']);

//include cockpit
include_once('admin/bootstrap.php');

$SITE = new LimeExtra\App($CONFIG);

$SITE['config'] = $CONFIG;

// utilize cockpits data storage
$SITE->service('db', function() {
    return cockpit()->db;
});

$SITE->service('cockpit', function() {
    return cockpit();
});

// register paths

$TEMP_PATH = cockpit()->path('tmp:');

$SITE->path('tmp'     , $TEMP_PATH);
$SITE->path('pages'   , __DIR__.'/site/pages');
$SITE->path('snippets', __DIR__.'/site/snippets');
$SITE->path('themes'  , __DIR__.'/site/themes');
$SITE->path('assets'  , __DIR__.'/site/assets');
$SITE->path('modules' , __DIR__.'/site/modules');
$SITE->path('theme'   , __DIR__.'/site/themes/'.$CONFIG['theme']);
$SITE->path('storage' , __DIR__.'/storage');
$SITE->path('vendor'  , __DIR__.'/vendor');

// set cache path
$SITE("cache")->setCachePath($TEMP_PATH);
$SITE->renderer->setCachePath($TEMP_PATH);

// register custom routes
include(__DIR__.'/routes.php');

// load modules if folder exists
$SITE->loadModules(__DIR__.'/site/modules');

// include rapido
include(__DIR__.'/system/rapido.php');

$SITE->trigger('site.init')->run();

// only for benchmarking
register_shutdown_function(function() use($SITE){

    return;

    file_put_contents('log.txt', json_encode([
        'time' => microtime(true) - RAPIDO_TIME_START,
        'memory' => $SITE('utils')->formatSize(memory_get_usage() - RAPIDO_MEMORY_START),
    ], JSON_PRETTY_PRINT));
});
