<?php

function render_view($view, $params = []) {

    global $SITE;

    if(!$view) return false;

    // parse page meta data
    $sitemeta  = $SITE->viewvars["meta"];
    $meta      = read_view_meta(file_get_contents($view));
    $viewtime  = filemtime($view);

    $SITE->path('page', dirname($view));

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
        $cachedfile  = $SITE->path("tmp:{$cachefile}");
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

        $content = $SITE->view($view, $params);

        if($cache) {
            if(file_put_contents($SITE->path("tmp:").$cachefile, $content)) {
                $cachedfile = $this->path("tmp:{$cachefile}");
                touch($cachedfile, time()+$cachetime);
            }
        }
    }

    return $content;
}

function read_view_meta($content){

    global $SITE;

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
