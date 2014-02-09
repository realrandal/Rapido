<?php

global $site;

$config = include(__DIR__.'/config.php');

// embed cockpit

if(file_exists(__DIR__."/{$config['admin']}/bootstrap.php")) {
    require_once(__DIR__."/{$config['admin']}/bootstrap.php");
} else {
    echo "Please install Cockpit first!";
    exit;
}

date_default_timezone_set($config['timezone']);

$site = new LimeExtra\App($config);

$site["config"]  = $config;
$site["cockpit"] = $cockpit;

// register global paths
foreach ([
    'root'     => __DIR__,
    'content'  => __DIR__.'/content',
    'snippets' => __DIR__.'/storage/snippets',
    'cache'    => __DIR__.'/storage/cache',
    'tmp'      => __DIR__.'/storage/cache/tmp',
    'lib'      => __DIR__.'/lib',
    'assets'   => __DIR__.'/lib/assets',
    'modules'  => __DIR__.'/lib/modules',
    'vendor'   => __DIR__.'/lib/vendor',
    'themes'   => __DIR__.'/themes',
    'theme'    => __DIR__."/themes/{$config['theme']}",
] as $key => $path) { $site->path($key, $path); }


// set cache path
$site("cache")->setCachePath("cache:tmp"); 

// load extension modules
$site->loadModules(__DIR__.'/lib/modules');

// route to content mapping
$site->bind("/*", function() use($site) {
    return $site->module("core")->render_page($site["route"]);
});

// handle 404, 500
$site->on("after", function() use($site) {

    switch ($site->response->status) {
        case 500:
        case 404:
            $site->layout = false;
            $site->response->body = $site->view("theme:{$site->response->status}.php");
            break;
    }
});

$site->trigger('site.init')->run();
