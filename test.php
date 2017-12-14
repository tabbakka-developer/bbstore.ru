<?php
/**
 * Created by PhpStorm.
 * User: Vlad
 * Date: 15.12.2017
 * Time: 0:43
 */

$url = "https://www.instagram.com/p/BcsZsSWAc9Y/";

//$some_data = file_get_contents();

$agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, $agent);
curl_setopt($ch, CURLOPT_URL, $url);
$result=curl_exec($ch);

//var_dump($result);

$info = curl_getinfo($ch);

if($info['http_code'] == 200){
    $html = curl_multi_getcontent($ch);
    $get_this_bitch = explode("_sharedData = ", $html)[1];
    $almost_json_string = explode(";</script>", $get_this_bitch)[0];
    $fucking_json = json_decode($almost_json_string, true);
    $edges = $fucking_json['entry_data']['PostPage'][0]['graphql']['shortcode_media']['edge_sidecar_to_children']['edges'];

    $photo_count = count($edges);
    echo "\n\n";
    foreach ($edges as $edge){
        echo $edge['node']['display_url'];
        echo "\n\n";
    }
//    echo $almost_json_string;
}
curl_close($ch);
//var_dump($result);
//print_r($result);
//$get_this_bitch = explode("_sharedData = ", $result);
//echo $get_this_bitch;