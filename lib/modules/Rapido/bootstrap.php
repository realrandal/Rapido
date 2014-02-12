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

$this->module("rapido")->extend([

    "render_page" => function($view, $params = []) use($site) {

        if(!$view) return false;

        // parse page meta data

        $sitemeta  = $site->viewvars["meta"];
        $meta      = $this->read_file_meta(file_get_contents($view));
        $viewtime  = filemtime($view);

        if(count($meta)) {
            foreach($meta as $key => $val) {
                $sitemeta->{$key} = $val;
            }
        }

        $cache = isset($sitemeta->cache) && $sitemeta->cache ? $sitemeta->cache : false;

        // set layout
        $view .= $sitemeta->layout ? " with {$sitemeta->layout}" : false;

        if($cache) {

            $cachefile   = md5($view).'.page.html';
            $cachedfile  = $site->path("tmp:{$cachefile}");
            $cachetime   = is_numeric($cache) ? $cache : 0;

            if($cachedfile) {

                $cachemtime = filemtime($cachedfile);


                // invalidate cache
                if($cachemtime < $viewtime) {
                    $cachedfile = null;
                } elseif ($cachetime && $cachemtime<time()) {
                    $cachedfile = null;
                }
            }
        }

        if($cache && $cachedfile) {

            $content = file_get_contents($cachedfile);

        } else {

            // auto regions
            if(isset($sitemeta->regions)) {

                $regions = new \stdClass;

                foreach ((array) $sitemeta->regions as $region) {
                    $name = str_replace(['-',' ', '.'], '_', $region);
                    $regions->{$name} = cockpit("regions")->render($region);
                }

                $params["regions"] = $regions;
            }

            // auto galleries
            if(isset($sitemeta->galleries)) {

                $galleries = new \stdClass;

                foreach ((array) $sitemeta->galleries as $gallery) {
                    $name = str_replace(['-',' ', '.'], '_', $gallery);
                    $galleries->{$name} = cockpit("galleries")->gallery($gallery);
                }

                $params["galleries"] = $galleries;
            }

            $content = $site->view($view, $params);

            if($cache) {
                if(file_put_contents($site->path("tmp:").$cachefile, $content)) {
                    $cachedfile = $this->path("tmp:{$cachefile}");
                    touch($cachedfile, time()+$cachetime);
                }
            }
        }

        return $content;
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

                    $json = json_decode($value);
                    if(!is_null($json)) $value = $json;

                    $meta[$key] = $value;
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
    $content = str_replace('@cockpitjs', '<?php echo cockpit("restservice")->js_lib(); ?>', $content);
    $content = preg_replace('/(\s*)@thumbnail\((.+?)\)/', '$1<?php cockpit("mediamanager")->thumbnail($2); ?>', $content);
    $content = preg_replace('/(\s*)@form\((.+?)\)/', '$1<?php cockpit("forms")->form($2); ?>', $content);
    $content = preg_replace('/(\s*)@region\((.+?)\)/', '$1<?php echo cockpit("regions")->render($2); ?>', $content);

    return $content;
});