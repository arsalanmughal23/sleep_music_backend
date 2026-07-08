<html>
<head>


    <title>{{$media->name}}</title>
    <meta name="description" content="{{$media->name}}">

    <!-- Facebook Meta Tags -->
    <meta property="og:url" content="{{ env('APP_URL') }}/get-url/{{$media->id}}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{$media->name}}">
    <meta property="og:description" content="{{$media->name}}">
    <meta property="og:image" content="{{$media->image}}">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="{{$media->name}}">
    <meta property="twitter:domain" content="{{ env('APP_URL') }}">
    <meta property="twitter:url" content="{{ env('APP_URL') }}/get-url/{{$media->id}}">
    <meta name="twitter:title" content="{{$media->name}}">
    <meta name="twitter:description" content="{{$media->name}}">
    <meta name="twitter:image" content="{{$media->image}}">

</head>
<body>

</body>
</html>
