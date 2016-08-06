<!DOCTYPE html>
<html>
    <head>
        <title>GW2Nodes - Node Locations</title>

        <meta name="description" content="GW2Nodes allows players to share gathering locations for Guild Wars 2. Includes user submissions and permanent node locations." />
        <meta name="viewport" content="width=device-width, user-scalable=no" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-status-bar-style" content="black" />
        <meta property="og:url" content="https://gw2nodes.com" />
        <meta property="og:description" content="GW2Nodes allows you to share gathering locations for Guild Wars 2. Includes user submissions and permanent node locations." />
        <meta property="og:site_name" content="GW2Nodes" />
        <meta property="og:title" content="GW2Nodes - Node Locations" />
        <meta property="og:image" content="https://gw2nodes.com/opengraph.png" />

        <link rel="apple-touch-icon" href="/favicon.png" />
        <link rel="stylesheet" type="text/css" href="https://d1h9a8s8eodvjz.cloudfront.net/fonts/menomonia/08-02-12/menomonia.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="{{ elixir('css/app.css') }}">

    </head>
    <body>

        <nav class="Navigation">
            <span id="showsidebar" class="Navigation__toggle">
                <i class="fa fa-wrench"></i>
            </span>

            <div class="Navigation__header">
                <a class="Navigation__header--brand" href="/">GW2Nodes</a>
            </div>

            <div class="Banner">
                <span></span>
            </div>

            <span id="shownavigation" class="Navigation__toggle Navigation__toggle--right visible-xs">
                <i class="fa fa-bars"></i>
            </span>

            <ul class="Navigation__list">
                <li><span id="serverchange">Server</span></li>
                <li><a href="#" data-toggle="modal" data-target="#faq-dialog">FAQ</a></li>
                <li><a href="#" data-toggle="modal" data-target="#preferences-dialog">Preferences</a></li>
                <li><a href="#" data-toggle="modal" data-target="#itemfinder-dialog">Item finder</a></li>
            </ul>

            <ul class="Navigation__list Navigation__list--right">
                <li>
                    <a href="https://github.com/koenvu/gw2nodes" title="View on GitHub">
                        <i class="fa fa-github"></i> {{ $revision }}
                    </a>
                </li>
                <li class="hidden-xs">IGN: koenvu.7584</li>
                <li class="hidden-sm"><a href="https://twitter.com/koenvu">@koenvu</a></li>
            </ul>
        </nav>

        <div id="sidebar">
            <div id="sidebar-content">
                <div class="text-center Help">
                    <p class="lead">Node locations are supplied by the <strong>community</strong>. Can't find what you are looking for? <strong>Add some nodes</strong>, or try a different server!</p>
                </div>

                <div id="nodelist">
                    <nodes></nodes>
                </div>
            </div>
        </div>

        <div id="map"></div>

        <div class="Legend" class="hidden-xs">
            <div><span class="normal"></span> Regular node</div>
            <div><span class="rich"></span> Rich node</div>
            <div><span class="permanent"></span> Permanent node</div>
        </div>

        <div class="Notifications">
        </div>

        <div id="hostedwith" class="hidden-xs">Hosted with <a href="https://www.digitalocean.com/?refcode=fce21793aa0e">DigitalOcean</a></div>

        @include('_modals.node-create')

        @include('_modals.node-details')

        @include('_modals.preferences')

        @include('_modals.server')

        @include('_modals.faq')

        @include('_modals.itemfinder', ['itemsoi' => $itemsOfInterest])

        @if (config('bugsnag.js_api_key'))
            <script
              src="//d2wy8f7a9ursnm.cloudfront.net/bugsnag-2.min.js"
              data-apikey="{{ config('bugsnag.js_api_key') }}">
            </script>
        @endif

        @if (config('broadcasting.connections.pusher.key'))
            <script src="//js.pusher.com/3.2/pusher.min.js"></script>
            <script>
            var pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
              encrypted: true,
            });
            </script>
        @endif
        <script src="{{ elixir('js/all.js') }}"></script>
        <script src="{{ elixir('js/app.js') }}"></script>

        @if (config('services.google.analytics'))
            <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', '{{ config('services.google.analytics') }}', 'gw2nodes.com');
            ga('send', 'pageview');
            </script>
        @endif

    </body>
</html>
