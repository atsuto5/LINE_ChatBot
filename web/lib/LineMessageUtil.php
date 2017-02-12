<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 19:55
 */
class LineMessageUtil {

    public static function getTextMessage($message) {
        return array(array(
            "type" => "text",
            "text" => $message
        ));
    }

}