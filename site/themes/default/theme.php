<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $meta->title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- required assets -->
    @scripts(['assets:js/jquery.js', 'themes:default/vendor/uikit/js/uikit.min.js'])

    <!-- theme assets -->
    @scripts(['themes:default/css/theme.css', 'themes:default/js/theme.js'])

    @trigger("site.header")
</head>
    <body>

        <div class="site-header">
            <div class="uk-container uk-container-center">
                @snippet?("navigation")
                    @snippet("navigation")
                @end
            </div>
        </div>

        <div class="site-main">
            {{ $content_for_layout }}
        </div>

        <div class="site-footer">
            <div class="uk-container uk-container-center">
                @snippet?("footer")
                    @snippet("footer")
                @end
            </div>
        </div>

        @trigger("site.footer")
    </body>
</html>
