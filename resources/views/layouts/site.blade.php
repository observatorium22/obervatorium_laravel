<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Observatorium')</title>
    <link rel="icon" href="{{ asset('graphics/favicon-32.png') }}">
    <link rel="stylesheet" href="{{ asset('css/my_style_100.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile_override.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <script>
        (function () {
            var hash = window.location.hash;
            if (!hash) {
                return;
            }
            var rules = [
                [/^#\/(?:home|galery)$/, '/'],
                [/^#\/project_list$/, '/projects'],
                [/^#\/project_preview_item\/(\d+)/, '/projects/$1/preview'],
                [/^#\/project_item_image\/(\d+)\/(\d+)/, '/projects/$1?image=$2'],
                [/^#\/project_item_text\/(\d+)/, '/projects/$1'],
                [/^#\/news_list$/, '/news'],
                [/^#\/news_item_image\/(\d+)\/(\d+)/, '/news/$1/images?image=$2'],
                [/^#\/news_item(?:_mobile)?\/(\d+)/, '/news/$1'],
                [/^#\/blog_list$/, '/blog'],
                [/^#\/blog_item(?:_mobile)?\/(\d+)/, '/blog/$1'],
                [/^#\/about$/, '/about'],
                [/^#\/observatorium_item(?:_mobile)?\/(\d+)/, '/about/$1']
            ];
            for (var i = 0; i < rules.length; i++) {
                var match = hash.match(rules[i][0]);
                if (!match) {
                    continue;
                }
                var url = rules[i][1].replace('$1', match[1] || '').replace('$2', match[2] || '');
                window.location.replace(url);
                return;
            }
        })();
    </script>
</head>
<body class="page-{{ $section }}">
    <div id="mycanvas">
        @include('partials.menu')
        @yield('content')
    </div>
    <script src="{{ asset('js/site.js') }}"></script>
</body>
</html>
