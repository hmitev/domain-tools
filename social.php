<?php
class socialCount{
    private $url, $timeout;

    function __construct($url, $timeout = 10){
        $this->url = rawurlencode($url);
        $this->timeout = $timeout;
    }

    function getTweets(){
        $json_string = $this->get_data('http://urls.api.twitter.com/1/urls/count.json?url=' .
            $this->url);
        $json = @json_decode($json_string, true);
        return isset($json['count']) ? intval($json['count']) : 0;
    }

    function getLinkedin(){
        $json_string = $this->get_data("http://www.linkedin.com/countserv/count/share?url=$this->url&format=json");
        $json = @json_decode($json_string, true);
        return isset($json['count']) ? intval($json['count']) : 0;
    }

    function getFacebook(){
        $json_string = $this->get_data('http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' .
            $this->url);
        $json = @json_decode($json_string, true);

        $share_count = isset($json[0]['share_count']) ? intval($json[0]['share_count']) : 0;
        $like_count = isset($json[0]['like_count']) ? intval($json[0]['like_count']) : 0;
        $comment_count = isset($json[0]['comment_count']) ? intval($json[0]['comment_count']) : 0;
        $val = $share_count . "::" . $like_count . "::" . $comment_count;
        return $val;
    }

    function getPlusones(){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS,
            '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"http://' .
            rawurldecode($this->url) .
            '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        $curl_results = curl_exec($curl);
        curl_close($curl);
        $json = @json_decode($curl_results, true);
        return isset($json[0]['result']['metadata']['globalCounts']['count']) ? intval($json[0]['result']['metadata']['globalCounts']['count']) :
            0;
    }

    function getStumble(){
        $json_string = $this->get_data('http://www.stumbleupon.com/services/1.01/badge.getinfo?url=' .
            $this->url);
        $json = @json_decode($json_string, true);
        return isset($json['result']['views']) ? intval($json['result']['views']) : 0;
    }

    function getDelicious(){
        $purl = 'http://' . $this->url;
        $purl = sprintf('http://api.pinterest.com/v1/urls/count.json?url=%s', $purl);
        $response = $this->get_data($purl);
        $response = str_replace(array('(', ')'), '', $response);
        $response = str_replace("receiveCount", '', $response);
        if (!$json = @json_decode($response, true))
            return 0;
        return isset($json['count']) ? (int)$json['count'] : 0;
    }

    private function getData($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        $cont = curl_exec($ch);
        if (curl_error($ch))
        {
            echo curl_error($ch) . "<br> <br>";
        }
        return $cont;
    }
}

?>
