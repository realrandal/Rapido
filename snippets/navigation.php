
<nav class="site-nav-main uk-navbar">

    <a class="uk-navbar-brand" href="@route('/')"><i class="uk-icon-bolt"></i> Rapido</a>

    <div class="uk-navbar-flip">
        <ul class="uk-navbar-nav">
            <li class="{{ $meta->route=='/docs'  ? 'uk-active':'' }}"><a href="@route('/docs')">Documentation</a></li>
            <li><a href="@base('/admin')"><i class="uk-icon-cogs"></i> <span class="uk-hidden-small">Backend</span></a></li>
        </ul>
        <ul class="uk-navbar-nav nav-secondary">
            <li><a href="https://github.com/aheinze/Rapido"><i class="uk-icon-github"></i> Source</a></li>
        </ul>
    </div>


</nav>