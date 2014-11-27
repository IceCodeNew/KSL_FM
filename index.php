<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Key Sounds Label 电台</title>
    <link rel="stylesheet" type="text/css" href="css/fa.css" />
    <link rel="stylesheet" type="text/css" href="css/fm.css" />
    <script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
</head>
<body>
    <div class="control">
        <div class="home"><i class="fa fa-home" title="Home"></i></div>
        <div class="next"><i class="fa fa-chevron-right" title="Next"></i></div>
    </div>
    <div class="container">
        <div class="player">
            <div class="cd">
                <div id="album" class="album"></div>
                <div class="center">
                    <span class="start"><i id="m_play" class="fa fa-play"></i></span>
                </div>
                <span class="progress">
                    <span class="current"></span>
                </span>
            </div>
            <section class="title">
                <h1 class="name"></h1>
                <h2 class="sub-title"></h2>
            </section>
            <audio id="player" src=""></audio>
        </div>
    </div>
    <script type="text/javascript" src="js/fm.js"></script>
    <script>
    <?php
    if(($_GET['album']) != '')
        echo "function load_music() {\$.get('player.php', {'album':'" . $_GET['album']. "'}, load_music_and_play_less_info);}"; 
    else
        echo "function load_music() {\$.get('player.php', load_music_and_play);}"; 
    ?>    
    </script>
    
</body>
</html>
