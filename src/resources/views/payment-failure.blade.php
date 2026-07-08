<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link href='https://fonts.googleapis.com/css?family=Lato:300,400|Montserrat:700' rel='stylesheet' type='text/css'>
    <style>
        @import url(//cdnjs.cloudflare.com/ajax/libs/normalize/3.0.1/normalize.min.css);
        @import url(//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css);
    </style>
    <link rel="stylesheet" href="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/default_thank_you.css">
    <script src="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/jquery-1.9.1.min.js"></script>
    <script src="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/html5shiv.js"></script>
    <style>
        .button {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        .button2 {
            background-color: #008CBA;
        }

        /* Blue */
        .button3 {
            background-color: #f44336;
        }

        /* Red */
        .button4 {
            background-color: #e7e7e7;
            color: black;
        }

        /* Gray */
        .button5 {
            background-color: #555555;
        }

        /* Black */
    </style>
</head>
<body>
<header class="site-header" id="header">
    <h1 class="site-header__title" data-lead-id="site-header-title">Declined!</h1>
</header>

<div class="main-content">
    <i class="fa fa-times main-content__checkmark" id="checkmark" style="color: red;"></i>
    <p class="main-content__body" data-lead-id="main-content-body">Thanks a bunch for filling that out. It means a lot
        to us, just like you do! We really appreciate you giving us a moment of your time today. Thanks for being
        you.</p>
    <br>
    <button class="button button5" id="trigger-android">Go to Home</button>
</div>

<footer class="site-footer" id="footer">
    <p class="site-footer__fineprint" id="fineprint">Copyright ©2020 | All Rights Reserved</p>
</footer>

<script type="text/javascript">
    $(function () {
        $("#trigger-android").on('click', function (e) {
            e.preventDefault();
            if (typeof Android !== "undefined") {
                Android.onFailure();
            }
            if (typeof window.webkit !== "undefined") {
                window.webkit.messageHandlers.failureHandler.postMessage({status: false});
            }
        });
        setTimeout(function () {
            if (typeof Android !== "undefined") {
                Android.onFailure();
            }
            if (typeof window.webkit !== "undefined") {
                window.webkit.messageHandlers.failureHandler.postMessage({status: false});
            }
        }, 3000)
    })
</script>
</body>

</html>