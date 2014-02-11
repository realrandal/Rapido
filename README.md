Rapido
======

Rapido is a flexible CMS implementation built on top of Cockpit


### Requirements

* PHP >= 5.4
* PDO + SQLite

make also sure that

    $_SERVER['DOCUMENT_ROOT']

exists and is set correctly. If you're running Apache you will also require mod_rewrite to be enabled.


### Installation

1. First download and extract the latest version of Rapido.
2. Upload the files to your server.
3. That's it. Tweak the .htaccess file if required.

You can override the default Rapido settings (and add your own custom settings) by editing config.php in the root Rapido directory.
The config.php file lists all of the settings and their defaults. To override a setting simply uncomment it in config.php and set your custom value.


### Creating Content

Rapido is a flat file CMS, this means there is no database to deal with. You simply create .php files in the "content" folder and that becomes a page.

If you created folder within the content folder (e.g. content/sub) and put an index.php inside it, you can access that folder at the URL http://yousite.com/sub. If you want another page within the sub folder, simply create a text file with the corresponding name (e.g. content/sub/page.php) and will be able to access it from the URL http://yousite.com/sub/page.

Below we've shown some examples of content locations and their corresponing URL's:

<table>
    <tbody>
        <tr>
            <th align="left">Physical Location</th>
            <th align="left">URL</th>
        </tr>
        <tr>
            <td>content/index.php</td>
            <td>/</td>
        </tr>
        <tr>
            <td>content/sub.php</td>
            <td>/sub</td>
        </tr>
        <tr>
            <td>content/sub/page.php</td>
            <td>/sub/page</td>
        </tr>
        <tr>
            <td>content/a/very/long/url.php&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td>/a/very/long/url</td>
        </tr>
    </tbody>
</table>

If a file cannot be found, the file themes/:theme/404.php will be shown.

<hr/>

### View File Markup

View files are using the Lexy syntax. They can also contain regular HTML or PHP. At the top of text files you can place a meta block and specify certain attributes of the page. For example:

    ===
    // modify title or layout, define scripts to load
    title: Welcome
    layout: otherlayout.php
    scripts: ["path/to/style.css", "script.js"]

    // custom data
    key1: value1
    key2: value2
    ===

These values will be contained in the  <code>$meta</code> variable in themes/views.

<hr/>

### Themeing

You can create themes for your Rapido installation in the "themes" folder. Check out the default theme for an example of a theme. Rapido uses Lexy for it's templating engine (inspired by Blade). You can select your theme by setting the <code>theme</code> variable in config.php to your theme folder.

All themes must include a **theme.php** file to define the HTML structure of the theme, also a **404.php** and **500.php** file for the error layouts. Below are the Lexy variables/methods that are available to use in your theme:

- **$base_url** - access: base url
- **$meta** - access: $meta->title, $meta->layout, $meta->scripts, $meta->route
- _@base_('/url/to/file/based/to/basefolder')
- _@route_('/page/about')
- _@snippet_('snippetname')
- _@render_('/path/to/view')
- _@thumbnail_('/path/to/image.jpg', 100, 100)
- _@region_('regionname') - region managed by Cockpit
- _@form_('formname') - using Cockpit's form api

<hr/>

### Contribute

Help make Rapido better by checking out the GitHub repoistory and submitting pull requests. If you find a bug please report it on the issues page.