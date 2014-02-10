<?php

// get realpath to docsroot
$docsroot = str_replace(DIRECTORY_SEPARATOR, '/', isset($_SERVER['DOCUMENT_ROOT']) ? (is_link($_SERVER['DOCUMENT_ROOT']) ? readlink($_SERVER['DOCUMENT_ROOT']) : $_SERVER['DOCUMENT_ROOT']) : __DIR__);
$base     = "/".trim(str_replace($docsroot, '', str_replace(DIRECTORY_SEPARATOR, '/', __DIR__)), "/");
$base     = rtrim($base, '/');
$svrname  = isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : 'localhost';

return [
    // don't touch, please!
    "name"         => $svrname,
    "base_url"     => $base,
    "base_route"   => $base,
    "docs_root"    => $docsroot,

    // edit here:
    "version"      => "1.0.0",
    "admin"        => "admin", // cockpit folder name
    "autoload"     => new ArrayObject([__DIR__.'/lib/vendor']),
    "timezone"     => "UTC",
    "theme"        => "default",

    // default site meta
    "site.meta"    => [
        "title"    => "Rapida", // default page title
        "layout"   => "theme:theme.php",
        "scripts"  => []
    ],
];