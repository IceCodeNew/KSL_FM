<?php
include 'api.php';
include 'list.php';

if(($_GET['album']) != '')
	$playlist_list =array($playlist_list[$_GET['album']]);

foreach ($playlist_list as $key => $value) {
	if(file_exists('playlist_cache/'.$value.'.json')){
		$arr=json_decode(file_get_contents('playlist_cache/'.$value.'.json'),true);
	}
	else{
    	$json = get_playlist_info($value);
    	$arr = json_decode($json, true);
    	file_put_contents('playlist_cache/'.$value.'.json', json_encode($arr));
    }
    if(!in_array($key, $playlist_cache)){
    	$playlist_cache[$key]=$arr;
    }

    foreach ($arr["result"]["tracks"] as $key2) {
        $id = $key2["id"];
        if (!in_array($id, $player_list)) {
            $player_list[] = $id;
            #$song_cache[$id]=$key2;
        }
    }
}


$id = get_music_id();
if(file_exists('song_cache/'.$id.'.json')){
	$music_info=json_decode(file_get_contents('song_cache/'.$id.'.json'),true);
}
else{
	$music_info = json_decode(get_music_info($id), true);
	file_put_contents('song_cache/'.$id.'.json', json_encode($music_info));
}
#echo json_encode($music_info);
$play_info["cover"] = $music_info["songs"][0]["album"]["picUrl"];
$play_info["mp3"] = $music_info["songs"][0]["mp3Url"];
$play_info["mp3"] = str_replace("http://m", "http://p", $play_info["mp3"]);
$play_info["title"] = $music_info["songs"][0]["name"];
$play_info["artist"] = $music_info["songs"][0]["artists"][0]["name"];
$play_info["album"] = $music_info["songs"][0]["album"]["name"];
echo json_encode($play_info);
?>
