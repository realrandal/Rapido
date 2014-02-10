
<div class="uk-clearfix">
    <ul class="uk-float-right site-nav-top  uk-subnav uk-subnav-line">
        <li><a href="https://github.com/aheinze/rapida"><i class="uk-icon-github"></i> Source</a></li>
        <li><a href="http://getcockpit.com">Cockpit</a></li>
    </ul>
</div>

<nav class="site-nav-main uk-navbar">

    <a class="uk-navbar-brand" href="@route('/')"><i class="uk-icon-bolt"></i> Rapido</a>

    <div class="uk-navbar-flip">
        <ul class="uk-navbar-nav">
            <li class="{{ $meta->route=='/about' ? 'uk-active':'' }}"><a href="@route('/about')">About</a></li>
            <li class="{{ $meta->route=='/docs'  ? 'uk-active':'' }}"><a href="@route('/docs')">Docs</a></li>
        </ul>
    </div>

</nav>