<?php

$site = $this;

// Events

$site->on("site.init", function() use($site){
    $site->viewvars["meta"] = (object) $site["config/site.meta"];

    $site->viewvars["meta"]->route = $site["route"];
    $site->viewvars["meta"]->site  = $site;
}, 100);

$site->on("site.header", function() use($site){

    echo '<base href="'.$site->baseUrl('/').'">';

    //add scripts defined in meta
    if(count($site->viewvars["meta"]->scripts)) {
        echo $site->assets($site->viewvars["meta"]->scripts, $site['config/version']);
    }

    $site->block("site.header");
});

$site->on("site.footer", function() use($site){
    $site->block("site.footer");
});

// API

$this->module("rapida")->extend([

    "render_page" => function($route) use($site) {

        $view = false;

        $path = rtrim(str_replace('../', '', $route), '/');
        $path = strlen($path) ? $path:'/';
        $path = rtrim($site->path("content:{$path}"), '/');

        // prevent direct access to files in the content folder
        if($path && is_file($path)) {
            return false;
        }

        if ($path && is_dir($path)) {

            if(file_exists("{$path}/index.php")) {
                $view = "{$path}/index.php";
            }

        } else {
            $tmp = explode('/', $route);
            $tmp[count($tmp)-1] .= ".php";
            $path = implode($tmp);
            $view =  $site->path("content:{$path}");
        }

        if($view) {

            $sitemeta = $site->viewvars["meta"];
            $meta     = $this->read_file_meta(file_get_contents($view));

            foreach($meta as $key => $val) {
                $sitemeta->{$key} = $val;
            }

            // set layout
            $view .= $sitemeta->layout && !$site->req_is("ajax") ? " with {$sitemeta->layout}" : false;

            return $site->view($view);
        }

        return false;
    },

    "read_file_meta" => function($content) use ($site) {

        global $site;

        $meta  = [];
        $lines = explode("\n", $content);

        if(trim($lines[0])=='===') {
            for($i=1;$i<count($lines);$i++) {

                $line = trim($lines[$i]);

                if(!strlen($line)) continue;
                if($line=='===') break;

                $parts = explode(":", $line, 2);

                if(count($parts)==2) {

                    $key   = trim($parts[0]);
                    $value = trim($parts[1]);

                    $meta[$key] = $value;

                    $json = json_decode($value);

                    if(!is_null($json)) {
                        $meta[$key] = $json;
                    }
                }
            }
        }

        return $meta;
    }

]);

// Extend view renderer

$site->renderer()->extend(function($content){

    // remove header meta dada
    if(substr($content, 0, 3)=='===') {
        $lines = explode("\n", $content);
        $end   = 0;

        for($i=1;$i<count($lines);$i++) {
            $end = $i;
            if(trim($lines[$i])=="===") break;
        }

        while( $end >= 0) {
            array_shift($lines);
            $end = $end - 1;
        }

        $content = implode("\n", $lines);
    }

    // parse markdown
    if(strpos($content, '<markdown>')!==false) {
        $content = preg_replace_callback("/<markdown>(.*?)<\/markdown>/smU", function($match){
            return \Parsedown::instance()->parse($match[1]);
        }, $content);
    }

    // snippets
    $content = preg_replace('/(\s*)@snippet\([",\'](.+?)[",\']\)/', '$1<?php echo $app->view("snippets:$2.php"); ?>', $content);

    // extend lexy parser with cockpit functions
    $content = preg_replace('/(\s*)@thumbnail\((.+?)\)/', '$1<?php cockpit("mediamanager")->thumbnail($2); ?>', $content);
    $content = preg_replace('/(\s*)@form\((.+?)\)/', '$1<?php cockpit("forms")->form($2); ?>', $content);
    $content = preg_replace('/(\s*)@region\((.+?)\)/', '$1<?php echo cockpit("regions")->render($2); ?>', $content);
    return $content;
});