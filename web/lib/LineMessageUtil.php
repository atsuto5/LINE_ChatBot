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

	/**
	 * @param $originUrl
	 * @param $previewUrl
	 * @return array
	 */
    public static function getImageMessage($originUrl,$previewUrl) {
        return array(
            "type" => "image",
            "originalContentUrl" => $originUrl,
            "previewImageUrl" => $previewUrl
        );
    }

    public static function getVideoMessage($originUrl,$previewUrl) {
        return array(
            "type" => "video",
            "originalContentUrl" => $originUrl,
            "previewImageUrl" => $previewUrl
        );
    }

    public static function getAudioMessage($originUrl,$duration) {
        return array(
            "type" => "audio",
            "originalContentUrl" => $originUrl,
            "duration" => $duration
        );
    }

    public static function getLocationMessage($title,$address,$latitude,$longitude) {
        return array(
            "type" => "location",
            "title" => $title,
            "address" => $address,
            "latitude" => $latitude,
            "longitude" => $longitude
        );
    }

    public static function getStickerMessage($packageId, $stickerId) {
        return array(
            "type" => "sticker",
            "packageId" => $packageId,
            "stickerId" => $stickerId
        );
    }

    /**
     * @param $altText
     * @param TemplateInterface $template
     */
    public static function getTemplateMessage($altText,$template) {
        $template->getTemplateObject();

    }


//    public static function getUriActionMessage($linkUri,$imageMapAreaObject) {
//        return array (
//            "type" => "uri",
//            "linkUrk" => $linkUri,
//            "area" => $imageMapAreaObject
//        );
//
//    }
//
//    public static function getImageMapAreaObject($x,$y,$width,$height) {
//        return array(
//            "x" => $x,
//            "y" => $y,
//            "width" => $width,
//            "height" => $height
//        );
//    }
//
//    public static function getMessageActionMessage($text,$imageMapAreaObject) {
//        return array (
//            "type" => "message",
//            "text" => $text,
//            "area" => $imageMapAreaObject
//        );
//    }







}