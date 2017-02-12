<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 23:20
 */
class LineClient {

    private $accessToken;

    public function __construct(){
        $this->accessToken = getenv("LINE_ACCESS_TOKEN");
    }

    public function send($replyToken,$messages) {
        $postData = [
            "replyToken" => $replyToken,
            "messages" => $messages
        ];

        $this->sendCurlMessage($postData);
    }

    private function sendCurlMessage($postData) {
        $ch = curl_init("https://api.line.me/v2/bot/message/reply");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charser=UTF-8',
            'Authorization: Bearer ' . $this->accessToken
        ));
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
        curl_setopt($ch, CURLOPT_PROXYPORT, '80');
        curl_setopt($ch, CURLOPT_PROXY, getenv("FIXIE_URL_ONLY"));
        $result = curl_exec($ch);
        error_log(json_encode($result));
        curl_close($ch);
    }

}