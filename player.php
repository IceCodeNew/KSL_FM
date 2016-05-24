<?php
include 'api.php';
include 'list.php';
$dir_path='.';
// $dir_path=getenv('MOPAAS_FILESYSTEM27563_LOCAL_PATH').'/'.getenv('MOPAAS_FILESYSTEM27563_NAME'); #mopaas virtual filesystem
$playlist_cache_path=$dir_path.'/playlist_cache/';
// $song_cache_path=$dir_path.'/song_cache/';
$lyric_cache_path=$dir_path.'/lyric_cache/';
if(!file_exists($playlist_cache_path)){
    mkdir($playlist_cache_path);
}
// if(!file_exists($song_cache_path)){
//     mkdir($song_cache_path);
// }
if(!file_exists($lyric_cache_path)){
    mkdir($lyric_cache_path);
}

#random pick an album, or a specific album
if(($_GET['album']) != ''){
    $rand_key = $_GET['album'];
    if(array_key_exists($rand_key, $playlist_list)){
        $playlist_id = $playlist_list[$rand_key];
    }
}
else{
    $playlist_keys = array_keys($playlist_list);
    $playlist_count = count($playlist_keys);
    $rand_key = $playlist_keys[rand(0, $playlist_count - 1)];
    $playlist_id = $playlist_list[$rand_key];
}

if(file_exists($playlist_cache_path.$playlist_id.'.json')){
        $arr=json_decode(file_get_contents($playlist_cache_path.$playlist_id.'.json'),true);
    }
else{
    $json = get_playlist_info($playlist_id);
    $arr = json_decode($json, true);
    file_put_contents($playlist_cache_path.$playlist_id.'.json', json_encode($arr));
}

// print_r($arr);
$playlist_result=$arr["result"];
$trackCount = $playlist_result["trackCount"];
$rand_track = $playlist_result["tracks"][rand(0, $trackCount - 1)];
$rand_track_id = $rand_track["id"];
$play_info["cover"] = 'img/'.$rand_key.'.jpg';//$playlist_result["coverImgUrl"];
$play_info["ksl_id"] = $rand_key;
$play_info["album"]=$rand_track["album"]["name"];
$play_info["title"]=$rand_track["name"];
$play_info["artist"]=$rand_track["artists"][0]["name"];
// $play_info["mp3"]=$rand_track["mp3Url"];
$play_info["mp3"]=encrypted_url($rand_track["lMusic"]["dfsId"]);
$play_info["sid"]=$rand_track["id"];

if(file_exists($lyric_cache_path.$rand_track_id.'.json')){
        $lyric_info = json_decode(file_get_contents($lyric_cache_path.$rand_track_id.'.json'),true);
    }
else{
    $json = get_music_lyric($rand_track_id, true);
    $lyric_info = json_decode($json, true);
    file_put_contents($lyric_cache_path.$rand_track_id.'.json', json_encode($lyric_info));
}

//处理歌词
if (isset($lyric_info["lrc"]["lyric"])) {
    $lrc = explode("\n", $lyric_info["lrc"]["lyric"]);
    $lrc_slot_per_sec=5; //200ms
    array_pop($lrc);
    foreach ($lrc as $rows) {
        $row = explode("]", $rows);
        if (count($row) == 1) {
            $play_info["lrc"] = " ";
            break;
        } else {
            $lyric = array();
            $col_text = end($row);
            array_pop($row);
            foreach ($row as $key) {
                $time = explode(":", substr($key, 1));
                $time = ($time[0] * 60 + $time[1])*$lrc_slot_per_sec;
                $play_info["lrc"][$time] = $col_text;
            }
        }
    }
} else {
    $play_info["lrc"] = " ";
}

if (isset($lyric_info["tlyric"]["lyric"])) {
    $lrc = explode("\n", $lyric_info["tlyric"]["lyric"]);
    $lrc_slot_per_sec=5; //200ms
    array_pop($lrc);
    foreach ($lrc as $rows) {
        $row = explode("]", $rows);
        if (count($row) == 1) {
            $play_info["tlrc"] = " ";
            break;
        } else {
            $lyric = array();
            $col_text = end($row);
            array_pop($row);
            foreach ($row as $key) {
                $time = explode(":", substr($key, 1));
                $time = ($time[0] * 60 + $time[1])*$lrc_slot_per_sec;
                $play_info["tlrc"][$time] = $col_text;
            }
        }
    }
} else {
    $play_info["tlrc"] = " ";
}

echo json_encode($play_info);
?>
