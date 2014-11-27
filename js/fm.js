//缺少错误处理, 把ID搞錯的話, 就手動點擊下一首吧

oAudio = document.getElementById('player');
btn = $("#m_play");
album = $("#album");


$('.control .home').click(function(){
    window.open('http://music.163.com/#/user/event?id=44366604');
})
$('.control .next').click(function(){
    oAudio.pause();
    next_music();
})
$('.container .center').click(function(){
    m_play();
})
$("#player").bind("ended", function () {
    next_music();
});

function update_progress() {
        $('body').width() > 422 ? $('.progress .current').css({'width': oAudio.currentTime / oAudio.duration * 100 + '%'}) + ($('.album img').css('opacity') != 1 ? $('.album img').css({'opacity': 1}) : '') : $('.album img').css({'opacity': 1.1 - oAudio.currentTime / oAudio.duration});
}

function m_play() {
	if (oAudio.currentSrc==""){
		next_music();
	}
    if (oAudio.paused) {
        oAudio.play();
        btn.attr("class", "fa fa-pause");
        album.addClass("playing");
        album.removeClass("paused");
    }
    else {
        oAudio.pause();
        btn.attr("class", "fa fa-play");
        album.addClass("paused");
    }
}

function next_music() {
    album.removeClass("paused");
    album.removeClass("playing");
    load_music();
    btn.attr("class", "fa fa-pause");
    album.addClass("playing");
}

function load_music_and_play(data){
    music_info = JSON.parse(data);
    $("#player").attr("src", music_info.mp3);
    $("#album").css("background-image", "url('" + music_info.cover + "')");
    $('.title h1').html(music_info.title);
    $('.title h2').html(music_info.artist+" &mdash; "+music_info.album);
    oAudio.addEventListener('timeupdate', update_progress, false);
    oAudio.play();
}

function load_music_and_play_less_info(data){
    music_info = JSON.parse(data);
    $("#player").attr("src", music_info.mp3);
    $("#album").css("background-image", "url('" + music_info.cover + "')");
    $('.title h1').html(music_info.title);
    $('.title h2').html(music_info.artist);
    oAudio.addEventListener('timeupdate', update_progress, false);
    oAudio.play();
}

// function load_music() {
//     $.get("player.php",{'A':'KA'}, load_music_and_play);
// }

//window.onload = next_music;

