<?php
include 'api.php';
include 'list.php';
if(($_GET['album']) != '')
	$playlist_list =array($playlist_list[$_GET['album']]);

foreach ($playlist_list as $key) {
    $json = get_playlist_info($key);
    $arr = json_decode($json, true);
    foreach ($arr["result"]["tracks"] as $key2) {
        $id = $key2["id"];
        if (!in_array($id, $player_list)) {
            $player_list[] = $id;
        }
    }
}

$id = get_music_id();
$music_info = json_decode(get_music_info($id), true);
#echo json_encode($music_info);
$play_info["cover"] = $music_info["songs"][0]["album"]["picUrl"];
$play_info["mp3"] = $music_info["songs"][0]["mp3Url"];
$play_info["mp3"] = str_replace("http://m", "http://p", $play_info["mp3"]);
$play_info["title"] = $music_info["songs"][0]["name"];
$play_info["artist"] = $music_info["songs"][0]["artists"][0]["name"];
$play_info["album"] = $music_info["songs"][0]["album"]["name"];
echo json_encode($play_info);
?>
