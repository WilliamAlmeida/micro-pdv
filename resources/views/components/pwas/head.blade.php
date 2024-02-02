<!-- PWA -->
<!-- Web Application Manifest -->
<link rel="manifest" href="{{ asset('manifest.json') }}">

<!-- Chrome for Android theme color -->
<meta name="theme-color" content="#000000">
<meta name="msapplication-starturl" content="{{ route('admin.dashboard') }}">

<!-- Add to homescreen for Chrome on Android -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="application-name" content="{{ env('APP_NAME') }}">
<link href="{{ asset('icons/icon-192x192.png') }}" rel="icon" sizes="192x192">

<!-- Add to homescreen for Safari on iOS -->
<meta name="apple-mobile-web-app-status-bar-style" content="#000000">
<meta name="apple-mobile-web-app-title" content="{{ env('APP_NAME') }}">

<link href="{{ asset('icons/icon-512x512.png') }}" rel="apple-touch-icon">

<link href="{{ asset('icons/splash-320x568.png') }}" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />

<!-- iPhone X (1125px x 2436px) -->
<link rel="apple-touch-startup-image" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)" href="{{ asset('icons/splash-1125x2436.png') }}">
<!-- iPhone 8, 7, 6s, 6 (750px x 1334px) -->
<link rel="apple-touch-startup-image" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)" href="{{ asset('icons/splash-750x1334.png') }}">
<!-- iPhone 8 Plus, 7 Plus, 6s Plus, 6 Plus (1242px x 2208px) -->
<link rel="apple-touch-startup-image" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3)" href="{{ asset('icons/splash-1242x2208.png') }}">
<!-- iPhone 5 (640px x 1136px) -->
<link rel="apple-touch-startup-image" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" href="{{ asset('icons/splash-640x1136.png') }}">
<!-- iPad Mini, Air (1536px x 2048px) -->
<link rel="apple-touch-startup-image" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)" href="{{ asset('icons/splash-1536x2048.png') }}">
<!-- iPad Pro 10.5" (1668px x 2224px) -->
<link rel="apple-touch-startup-image" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)" href="{{ asset('icons/splash-1668x2224.png') }}">
<!-- iPad Pro 12.9" (2048px x 2732px) -->
<link rel="apple-touch-startup-image" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)" href="{{ asset('icons/splash-2048x2732.png') }}">

<meta name="apple-mobile-web-app-capable" content="yes">

<!-- Tile for Win8 -->
<meta name="msapplication-TileColor" content="#000000">
<meta name="msapplication-TileImage" content="{{ asset('icons/icon-512x512.png') }}">

<!-- TWITTER SHARE -->
{{-- <meta name="twitter:card" content="summary_large_image">
<meta name="twitter:description" content="Conheça o Wil PDV, a solução em nuvem para restaurantes, mercados, lojas de roupas, petshops, lanchonetes, sorveterias e outros segmentos. Simplifique sua gestão de vendas e estoque com nosso sistema PDV intuitivo e eficiente.">
<meta name="twitter:title" content="{{ env('APP_NAME') }}">
<meta name="twitter:site" content="@wilpdv">
<meta name="twitter:domain" content="{{ env('APP_NAME') }}">
<meta name="twitter:creator" content="@wilpdv"> --}}

<!-- FACEBOOK SHARE -->
{{-- <meta property="fb:app_id"         		content="273921101283507" /> 	
<meta property="og:locale"              content="pt_BR" />
<meta property="og:url"                 content="{{ route('home') }}" />
<meta property="og:title"               content="{{ env('APP_NAME') }}" />
<meta property="og:site_name"           content="{{ env('APP_NAME') }}" />
<meta property="og:description"         content="Conheça o Wil PDV, a solução em nuvem para restaurantes, mercados, lojas de roupas, petshops, lanchonetes, sorveterias e outros segmentos. Simplifique sua gestão de vendas e estoque com nosso sistema PDV intuitivo e eficiente." />
<meta property="og:image"       		content="{{ asset('images/site/bg_03.png') }}" itemdrop="image" />
<meta property="og:image:alt"       	content="{{ env('APP_NAME') }}" />
<meta property="og:image:type"			content="image/png" />
<meta property="og:image:width" 		content="600" />
<meta property="og:image:height" 		content="315" />
<meta property="og:type" 				content="website" />
<meta property="article:author" 		content="{{ env('APP_NAME') }}">
<meta property="article:section" 		content="Delivery">
<meta property="article:tag" 			content="{{ env('APP_NAME') }}">
<meta property="article:published_time" content="08/08/2021">
<meta name="facebook-domain-verification" content="kv7xitos95ytko5rqwv2mnb95qlpwy" /> --}}

<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icons/favicon-16x16.png') }}">
<link rel="manifest" href="{{ asset('icons/site.webmanifest') }}">
<link rel="mask-icon" href="{{ asset('icons/safari-pinned-tab.svg') }}" color="#cc3333">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="theme-color" content="#ffffff">

<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">