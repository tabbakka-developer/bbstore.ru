<?php
/**
 * Created by PhpStorm.
 * User: Vlad
 * Date: 15.12.2017
 * Time: 1:15
 */

namespace bb_store;


class InstagramThief{

    private $ch;
    private $url;
    private $agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

    /**
     * InstagramThief constructor.
     * @param $instagramPostURL string URL of Instagram post
     */
    public function __construct($instagramPostURL){
        $this->ch = curl_init();
        $this->url = $instagramPostURL;
    }

    public function GetPhotos(){
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_VERBOSE, true);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->agent);
        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        $result=curl_exec($this->ch);

        $info = curl_getinfo($this->ch);

        if($info['http_code'] == 200){
            $html = curl_multi_getcontent($this->ch);
            curl_close($this->ch);
            $get_this_bitch = explode("_sharedData = ", $html)[1];
            $almost_json_string = explode(";</script>", $get_this_bitch)[0];
            $fucking_json = json_decode($almost_json_string, true);
            if(json_last_error() == JSON_ERROR_NONE){
                $edges = $fucking_json['entry_data']['PostPage'][0]['graphql']['shortcode_media']['edge_sidecar_to_children']['edges'];

                $photo_count = count($edges);
                $photo_array = [];
                foreach ($edges as $edge){
                    array_push($photo_array, $edge['node']['display_url']);
                }

                return $photo_array;
            }
            else {
                return false;
            }

        }
        else {
            curl_close($this->ch);
            return false;
        }
    }


}