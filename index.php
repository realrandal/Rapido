<?php

global $site;

$config = include(__DIR__.'/config.php');

// embed cockpit

if(file_exists(__DIR__."/{$config['admin']}/bootstrap.php")) {
    require_once(__DIR__."/{$config['admin']}/bootstrap.php");
} else {
    echo "<center>Please install Cockpit first to the folder /{$config['admin']}!</center>";
    exit;
}

date_default_timezone_set($config['timezone']);

$site = new LimeExtra\App($config);

$site["config"] = $config;

// register global paths
foreach ([
    'root'     => __DIR__,
    'content'  => __DIR__.'/content',
    'snippets' => __DIR__.'/snippets',
    'data'     => __DIR__.'/storage/data',
    'cache'    => __DIR__.'/storage/cache',
    'tmp'      => __DIR__.'/storage/cache/tmp',
    'media'    => __DIR__.'/storage/media',
    'lib'      => __DIR__.'/lib',
    'assets'   => __DIR__.'/lib/assets',
    'modules'  => __DIR__.'/lib/modules',
    'vendor'   => __DIR__.'/lib/vendor',
    'themes'   => __DIR__.'/themes',
    'theme'    => __DIR__."/themes/{$config['theme']}",
    'cockpit'  => __DIR__."/{$config['admin']}",
] as $key => $path) { $site->path($key, $path); }

// nosql storage
$site->service('data', function() use($site) {
    $client = new MongoLite\Client($site->path('data:'));
    return $client;
});

// key-value storage
$site->service('memory', function() use($site) {
    $client = new RedisLite(sprintf("%s/site.memory.sqlite", $site->path('data:')));
    return $client;
});

// set cache path
$site("cache")->setCachePath("cache:tmp"); 

// load extension modules
$site->loadModules(__DIR__.'/lib/modules');

// route to content mapping
$site->bind("/*", function() use($site) {
    return $site->module("rapida")->render_page($site["route"]);
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