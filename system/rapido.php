<?php


include(__DIR__.'/functions.php');

// map pages to route
$SITE->bind("/*", function() {

    $view  = false;
    $route = str_replace('../', '', rtrim($this["route"], '/'));
    $path  = $this->path("pages:".(strlen($route) ? $route : '/'));

    // prevent direct access to files in the content folder
    if($path && is_file($path)) {
        return false;
    }

    if ($path && is_dir($path)) {

        // load index file if url points to folder
        if(file_exists("{$path}/index.php")) {
            $view = "{$path}/index.php";
        }

    } else {
        $view = $this->path("pages:{$route}.php");
    }

    return $view ? render_view($view) : false;
});

// system events
$SITE->on("site.init", function() {
    $this->viewvars["meta"] = (object) $this["config/site.meta"];
    $this->viewvars["meta"]->route = $this["route"];
    $this->viewvars["meta"]->site  = $this;
}, 100);

$SITE->on("site.header", function() {

    //add scripts defined in meta
    if(count($this->viewvars["meta"]->scripts)) {
        echo $this->assets($this->viewvars["meta"]->scripts, $this['config/version']);
    }

    $this->block("site.header");
});

$SITE->on("site.footer", function() {
    $this->block("site.footer");
});

// handle 404, 500
$SITE->on("after", function() {

    switch ($this->response->status) {
        case 500:
        case 404:
            $this->layout = false;
            $this->response->body = $this->view("theme:{$this->response->status}.php");
            break;
    }
});

// extend view renderer
$SITE->renderer->extend(function($content){

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

    // snippets
    $content = preg_replace('/(\s*)@snippet\([",\'](.+?)[",\']\)/', '$1<?php echo $app->view("snippets:$2.php"); ?>', $content);
    $content = preg_replace('/(\s*)@snippet\?\([",\'](.+?)[",\']\)/', '$1<?php if($app->path("snippets:$2.php")) { ?>', $content);

    // extend lexy parser with cockpit functions
    $content = str_replace('@cockpitjs', '<?php echo cockpit("restservice")->js_lib(); ?>', $content);
    $content = preg_replace('/(\s*)@assets\((.+?)\)/' , '$1<?php $app("assets")->style_and_script($2); ?>', $content);
    $content = preg_replace('/(\s*)@thumbnail\((.+?)\)/', '$1<?php cockpit("mediamanager")->thumbnail($2); ?>', $content);
    $content = preg_replace('/(\s*)@form\((.+?)\)/', '$1<?php cockpit("forms")->form($2); ?>', $content);
    $content = preg_replace('/(\s*)@region\((.+?)\)/', '$1<?php echo cockpit("regions")->render($2); ?>', $content);

    return $content;
});
