<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="icon" href="/favicon.ico" type="image/x-icon"> 
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <title>Key Sounds Label 电台</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="css/responsive.css" />
    <link rel="stylesheet" type="text/css" href="css/fa.css" />
    <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="js/jquery.hotkeys.js"></script>
</head>
<body>
    <div class="control-buttons">
        <div class="fa-button home-button"><span class="fa fa-home" title="Home"></span></div>
        <div class="fa-button next-button"><span class="fa fa-chevron-right" title="Next"></span></div>
    </div>
<main class="main">
        <div class="audio-player">
            <div class="audio-album paused">
                <img id="cover_img" src="img/key_logo.png" alt="album">
                <div class="shade-layer"><span class="fa fa-play"></span></div>
            </div>
            <div class="audio-progress">
                <div class="elapsed"></div>
            </div>
        </div>
        <div class="audio-info">
                <div id="title" class="title"></div>
                <div id="artist" class="artist"></div>
                <div id="ksl_id" class="ksl_id"></div>
                <div id="lrc" class="lrc"></div>
                <div id="tlrc" class="lrc"></div>
                <div id="volume" class="lrc"></div>
        </div>
        <audio id="audio" src=""></audio>
</main>
    <script type="text/javascript" src="js/fm.js"></script>
    <script>
    <?php
    if(($_GET['album']) != ''){
        echo "var album_ID='".$_GET['album']."';";
    }
    else{
        echo "var album_ID='';";
    	echo "window.onload = loadMusic(album_ID);";
    }
    ?>    
    </script>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-51644149-7', 'auto');
    ga('send', 'pageview');
    </script>
</body>
</html>
