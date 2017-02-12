<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 19:55
 */
class LineMessageUtil {

    public static function getTextMessage($message) {
        return array(
            "type" => "text",
            "text" => $message
        );
    }

    public static function getImageMessage($originUrl,$previewUrl) {
        return array(
            "type" => "image",
            "originalContentUrl" => $originUrl,
            "previewImageUrl" => $previewUrl
        );
    }

}