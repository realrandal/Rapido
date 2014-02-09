<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $meta->title }}</title>

    @scripts(['theme:assets/vendor/jquery.js','theme:assets/vendor/uikit/css/uikit.almost-flat.css', 'theme:assets/vendor/uikit/js/uikit.min.js'])
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