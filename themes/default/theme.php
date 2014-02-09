<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $meta->title }}</title>

    @assets(['theme:assets/vendor/uikit/css/uikit.almost-flat.css'], 'default.theme', 'cache:assets', 3600)
    @trigger("site.header")
</head>
    <body>
        
        <div class="uk-container uk-container-center uk-margin-top">
            
            @snippet("navigation")

            {{ $content_for_layout }}
        </div>

        @trigger("site.footer")
    </body>
</html>