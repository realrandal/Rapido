<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $meta->title }}</title>

    @scripts(['theme:assets/vendor/jquery.js', 'theme:assets/vendor/uikit/js/uikit.min.js'])
    @scripts(['theme:assets/css/theme.css', 'theme:assets/js/theme.js'])
    @trigger("site.header")
</head>
    <body>

        <div class="uk-container uk-container-center uk-margin-top">

            <div class="uk-margin">
                @snippet("navigation")
            </div>

            <div class="uk-margin">
                {{ $content_for_layout }}
            </div>
        </div>

        @trigger("site.footer")
    </body>
</html>