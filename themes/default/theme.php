<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>{{ $meta->title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @scripts(['theme:assets/vendor/jquery.js', 'theme:assets/vendor/uikit/js/uikit.min.js'])
    @scripts(['theme:assets/css/theme.css', 'theme:assets/js/theme.js'])
    @trigger("site.header")

</head>
    <body>

        <div class="site-header">
            <div class="uk-container uk-container-center">
                @snippet("navigation")
            </div>
        </div>

        <div class="site-main">
            <div class="uk-container uk-container-center">
                {{ $content_for_layout }}
            </div>
        </div>

        <div class="site-footer">
            <div class="uk-container uk-container-center">
                @snippet("footer")
            </div>
        </div>

        @trigger("site.footer")
    </body>
</html>