<!doctype html>
<html lang="{{ $page->translated->locale ?? 'en' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $page->translated->title ?? env('APP_NAME') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .flex-center {
            display: flex;
            justify-content: left;
            padding: 50px 100px;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .title {
            font-size: 60px;
            text-align: center;
            font-weight: normal;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        p {
            color: #111;
        }

        .clear {
            margin-top: 50px;
        }
        .content *{
            color: white;
        }
        .content {
            color: white;
            font-size: 2rem;
            background-color: #0E2851;
            padding: 5px 20px;
        }
    </style>
</head>
<body>
<h1 class="title">{{$page->translated->title ?? env('APP_NAME') }}</h1>

@if($page)
    <div class="content">    
        {!! $page->translated->content ?? '' !!}
    </div>
@else
    <div style="text-align:center; font-size:120px;">Error 404</div>
@endif

<div class="clear" style="padding:30px 0px;"></div>
</body>
</html>