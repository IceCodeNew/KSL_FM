<?php
function curl_get($url) {
    $refer = "http://music.163.com/";
    $header[] = "Cookie: " . "appver=2.0.2;";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_REFERER, $refer);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function encrypted_id($id) {
    $byte1 = str_split('3go8&$8*3*3h0k(2)2');
    $byte2 = str_split(strval($id));
    
    $byte1_len = count($byte1);
    $byte2_len = count($byte2);
    
    for($i = 0; $i < $byte2_len; $i++){
        $byte2[$i] = $byte2[$i] ^ $byte1[($i % $byte1_len)];
    } 
    $md5 = md5(implode($byte2), $raow_output=True);
    $result = base64_encode($md5);
    $result = str_replace('/', '_', $result);
    $result = str_replace('+', '-', $result);
    return $result;
}

function encrypted_url($id) {
    $eid = encrypted_id($id);
    return "http://p".rand(1,2).".music.126.net/".$eid."/".strval($id).".mp3";
}

function music_search($word, $type) {
    $url = "http://music.163.com/api/search/pc";
    $post_data = array(
        's' => $word,
        'offset' => '0',
        'limit' => '20',
        'type' => $type,
    );
    $referrer = "http://music.163.com/";
    $URL_Info = parse_url($url);
    $values = array();
    $result = '';
    $request = '';
    foreach ($post_data as $key => $value) {
        $values[] = "$key=" . urlencode($value);
    }
    $data_string = implode("&", $values);
    if (!isset($URL_Info["port"])) {
        $URL_Info["port"] = 80;
    }
    $request .= "POST " . $URL_Info["path"] . " HTTP/1.1\n";
    $request .= "Host: " . $URL_Info["host"] . "\n";
    $request .= "Referer: $referrer\n";
    $request .= "Content-type: application/x-www-form-urlencoded\n";
    $request .= "Content-length: " . strlen($data_string) . "\n";
    $request .= "Connection: close\n";
    $request .= "Cookie: " . "appver=1.5.0.75771;\n";
    $request .= "\n";
    $request .= $data_string . "\n";
    $fp = fsockopen($URL_Info["host"], $URL_Info["port"]);
    fputs($fp, $request);
    $i = 1;
    while (!feof($fp)) {
        if ($i >= 15) {
            $result .= fgets($fp);
        } else {
            fgets($fp);
            $i++;
        }
    }
    fclose($fp);
    return $result;
}

function get_music_info($music_id) {
    $url = "http://music.163.com/api/song/detail/?id=" . $music_id . "&ids=%5B" . $music_id . "%5D";
    return curl_get($url);
}

function get_artist_album($artist_id, $limit) {
    $url = "http://music.163.com/api/artist/albums/" . $artist_id . "?limit=" . $limit;
    return curl_get($url);
}

function get_album_info($album_id) {
    $url = "http://music.163.com/api/album/" . $album_id;
    return curl_get($url);
}

function get_playlist_info($playlist_id) {
    $url = "http://music.163.com/api/playlist/detail?id=" . $playlist_id;
    return curl_get($url);
}

function get_music_lyric($music_id) {
    $url = "http://music.163.com/api/song/lyric?os=pc&id=" . $music_id . "&lv=-1&kv=-1&tv=-1";
    return curl_get($url);
}

function get_mv_info() {
    $url = "http://music.163.com/api/mv/detail?id=319104&type=mp4";
    return curl_get($url);
}

function get_music_id() {
    $played = isset($_COOKIE["played"]) ? json_decode($_COOKIE["played"]) : null;
    $id = rand_music();
    if ($played != null) {
        global $player_list;
        $sum = count($player_list);
        if ($sum >= 2) {
            $sum = $sum * 0.5;
        } else {
            $sum -= 1;
        }
        while (in_array($id, $played)) {
            $id = rand_music();
        }
        if (count($played) >= $sum) {
            array_splice($played, 0, 1);
        }
    }
    $played[] = $id;
    setcookie("played", json_encode($played), time() + 120);
    return $id;
}

function rand_music() {
    global $player_list;
    $sum = count($player_list);
    $id = $player_list[rand(0, $sum - 1)];
    return $id;
}

?>
