<?php

$site = $this;

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

        // meta

        $meta = [];

        if($view) {
            
            $meta = array_merge([
                "title"    => $site["name"],
                "layout"   => "theme:theme.php",
                "base_url" => $site["base_url"],
                "scripts"  => []
            ], $site["config/page.meta"], $this->read_file_meta(file_get_contents($view)));

            // set layout
            $view .= $meta["layout"] && !$site->req_is("ajax") ? " with {$meta['layout']}" : false;

            if(!$site->req_is("ajax")) {

                //add scripts defined in meta
                $site->on("site.header", function() use($site, $meta){
                    if(count($meta["scripts"])) {
                        echo $site->assets($meta["scripts"], $site['config/version']);
                    }

                    $site->block("site.header");
                });

                $site->on("site.footer", function() use($site, $meta){
                    $site->block("site.footer");
                });
            }
        }

        $meta = (object) $meta;

        return $view ? $site->view($view, ["meta" => $meta]) : false;
    },

    "read_file_meta" => function($content) {
        
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


    $content = preg_replace('/(\s*)@snippet\([",\'](.+?)[",\']\)/', '$1<?php echo $app->view("snippets:$2.php", ["meta" => isset($meta) ? $meta:null]); ?>', $content);

    // extend lexy parser with cockpit functions
    $content = preg_replace('/(\s*)@thumbnail\((.+?)\)/', '$1<?php cockpit("mediamanager")->thumbnail($2); ?>', $content);
    $content = preg_replace('/(\s*)@form\((.+?)\)/', '$1<?php cockpit("forms")->form($2); ?>', $content);
    $content = preg_replace('/(\s*)@region\((.+?)\)/', '$1<?php cockpit("regions")->render($2); ?>', $content);
    return $content;
});