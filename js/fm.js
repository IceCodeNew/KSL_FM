var audio = $('#audio'),
    album = $('.audio-album'),
    cover = $('#cover_img'),
    title = $('#title'),
    artist = $('#artist'),
    ksl_id = $('#ksl_id'),
    lrc_row = $("#lrc"),
    tlrc_row = $("#tlrc"),
    elapsed = $('.elapsed'),
    shade = $('.shade-layer'),
    lrc = "",
    tlrc = "",
    lrc_interval,
    volume = $('#volume'),
    home = 'http://ksl.oldcat.me/index_music.html';  // homepage

jQuery(document).ready(function ($) {
    $(document).bind('keydown', 'n', function(){
        loadMusic(album_ID);
    });
    $(document).bind('keydown', 'right', function(){
        loadMusic(album_ID);
    });
    $(document).bind('keydown', 'p', function(){
        shade.click();
    });
    $(document).bind('keydown', 'space', function(){
        shade.click();
    });
    $(document).bind('keydown', 'up', function(){
        if(audio[0].volume<0.99){
            audio[0].volume += 0.01;
        }
        volume.html('volume:' + Math.round(100*audio[0].volume) + '%');
        setTimeout(function(){volume.html('')}, 500);
    });
    $(document).bind('keydown', 'down', function(){
        if(audio[0].volume>0.01){
            audio[0].volume -= 0.01;
        }
        volume.html('volume:' + Math.round(100*audio[0].volume) + '%');
        setTimeout(function(){volume.html('')}, 500);        
    });

    $('.control-buttons').on('click', '.fa-button', function () {
        var that = $(this);

        if (that.hasClass('home-button')) {
            window.open(home);
        }
        else if (that.hasClass('next-button')) {
            audio[0].pause();
            loadMusic(album_ID);
        }
        else if (that.hasClass('chrome-extension')) {
            window.open('https://chrome.google.com/webstore/detail/key-sounds-label-fm/hljmofdmkkbjcnegokhlhnginjambmpf');
        }
    });

    audio.on({
        'playing': function () {
            album.removeClass('paused');
            shade.find('.fa').removeClass('fa-play').addClass('fa-pause');
            if (lrc != " " || tlrc != " ") {
                lrc_interval = setInterval("display_lrc()", 200);
            }

        },
        'pause': function () {
            album.addClass('paused');
            shade.find('.fa').removeClass('fa-pause').addClass('fa-play');
            clearInterval(lrc_interval);
        },
        'ended': function () {
            clearInterval(lrc_interval);
            loadMusic(album_ID);
        },
        'timeupdate': function () {
            elapsed.css('width', audio[0].currentTime * 100 / audio[0].duration + '%');
        },
        'error': function () {
            // console.log(-1);
        }
    });

    audio[0].volume = 0.6;

    shade.click(function () {
        if (audio[0].paused){
        	if (audio[0].src.search('.mp3')<0){
        		loadMusic(album_ID);
        	}
        	else{
        		audio[0].play();
        	}    
        }
        else{
            audio[0].pause();
        } 
    });

    
});

function loadMusic(album_ID) {
    if (typeof album_ID === 'undefined') {
        album_ID = ''; 
    }
    $.getJSON('player.php?_=' + $.now()+'&album='+album_ID, function (data) {
        
        audio.attr('src', data.mp3);
        cover.attr({
            'src': data.cover + '?param=350y350',
            'data-src': data.cover
        });
        title.html(data.title);
        artist.html(data.artist);
        ksl_id.html(data.album);
        ksl_id.attr({
            'title' : data.ksl_id
        });
        audio[0].play();
        lrc = data.lrc;
        lrc_row.html(" ");
        tlrc = data.tlrc;
        tlrc_row.html(" ");

    });
}

function display_lrc(){
    var play_time = Math.floor(audio[0].currentTime*5).toString();
    lrc_row.html(lrc[play_time]);
    tlrc_row.html(tlrc[play_time]);    
};



