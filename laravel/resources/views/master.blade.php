<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stereo Room</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"/>
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="/js/logic.js"></script>
    <link rel="stylesheet" href="/css/styles.css"/>
    <link rel="shortcut icon" href="/images/favicon.png" type="image/x-icon" />

</head>
<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=1462113457446489";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
<span id="country" style="display: none;">{{$_SESSION['location']}}</span>

@yield("content")

</body>
</html>