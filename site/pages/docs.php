===

    title: Rapido Docs

===

<div class="uk-container uk-container-center">
    <div style="margin-bottom:100px;">

        <h1>Documentation</h1>

        <p class="uk-text-large uk-text-muted">
            Rapido is a flexible CMS implementation built on top of Cockpit
        </p>

    </div>


    <div class="uk-grid uk-margin" data-uk-margin>

        <div class="uk-width-medium-1-4">
            <ul class="uk-nav">
                <li class="uk-nav-header">Further information</li>
                <li><a href="https://github.com/aheinze/Rapido">Rapido on Github</a></li>
                <li><a href="http://getcockpit.com">Learn about Cockpit</a></li>
            </ul>
        </div>

        <div class="uk-width-medium-3-4">

            <h3>Requirements</h3>

            <p>To run Rapido you need PHP 5.4+ to be running on your server. If you're running Apache you will also require mod_rewrite to be enabled.</p>

            <hr/>

            <h3>Installation</h3>

            <ol>
                <li>First download and extract the latest version of Rapido.</li>
                <li>Upload the files to your server.</li>
                <li>That's it. Tweak the .htaccess file if required.</li>
            </ol>

            <p>You can override the default Rapido settings (and add your own custom settings) by editing config.php in the root Rapido directory.
            The config.php file lists all of the settings and their defaults. To override a setting simply uncomment it in config.php and set your custom value.</p>

            <h3>Creating Content</h3>

            <p>Rapido is a flat file CMS, this means there is no database server to deal with. You simply create .php files in the "content" folder and that becomes a page.</p>

            <p>If you created folder within the content folder (e.g. content/sub) and put an index.php inside it, you can access that folder at the URL http://yousite.com/sub. If you want another page within the sub folder, simply create a text file with the corresponding name (e.g. content/sub/page.php) and will be able to access it from the URL http://yousite.com/sub/page.</p>

            <p>Below we've shown some examples of content locations and their corresponing URL's:</p>

            <table class="uk-table uk-table-striped">
                <tbody>
                    <tr>
                        <th style="text-align:left" width="40%">Physical Location</th>
                        <th style="text-align:left">URL</th>
                    </tr>
                    <tr>
                        <td>site/pages/index.php</td>
                        <td>/</td>
                    </tr>
                    <tr>
                        <td>site/pages/sub.php</td>
                        <td>/sub</td>
                    </tr>
                    <tr>
                        <td>site/pages/sub/page.php</td>
                        <td>/sub/page</td>
                    </tr>
                    <tr>
                        <td>site/pages/a/very/long/url.php</td>
                        <td>/a/very/long/url</td>
                    </tr>
                </tbody>
            </table>

            <p>If a file cannot be found, the file themes/:theme/404.php will be shown.</p>

            <hr/>

            <h3>View File Markup</h3>

            <p>View files are using the Lexy syntax. They can also contain regular HTML or PHP. At the top of text files you can place a meta block and specify certain attributes of the page. For example:</p>

<pre><code>===
// modify title or layout, define scripts to load
title: Welcome
layout: otherlayout.php
scripts: ["path/to/style.css", "script.js"]

// custom data
key1: value1
key2: value2
===
</code></pre>

            <p>These values will be available in the  <code>$meta</code> variable in themes/views.</p>

            <hr/>

            <h3>Themeing</h3>

            <p>You can create themes for your Rapido installation in the "themes" folder. Check out the default theme for an example of a theme. Rapido uses Lexy for it's templating engine (inspired by Blade). You can select your theme by setting the <code>theme</code> variable in config.php to your theme folder.</p>

            <p>All themes must include a <strong>theme.php</strong> file to define the HTML structure of the theme, also a <strong>404.php</strong> and <strong>500.php</strong> file for the error layouts. Below are the Lexy variables/methods that are available to use in your theme:</p>

            <ul>
                <li><strong>$base_url</strong> - access: base url</li>
                <li><strong>$meta</strong> - access: $meta->title, $meta->layout, $meta->scripts, $meta->route</li>
                <li><em>@base</em>('/url/to/file/based/to/basefolder')</li>
                <li><em>@route</em>('/page/about')</li>
                <li><em>@snippet</em>('snippetname')</li>
                <li><em>@render</em>('/path/to/view')</li>
                <li><em>@thumbnail</em>('/path/to/image.jpg', 100, 100)</li>
                <li><em>@region</em>('regionname') - region managed by Cockpit</li>
                <li><em>@form</em>('formname') - using Cockpit's form api</li>
            </ul>

            <hr/>

            <h3>Contribute</h3>

            <p>Help make Rapido better by checking out the GitHub repoistory and submitting pull requests. If you find a bug please report it on the issues page.</p>

        </div>
    </div>
</div>
