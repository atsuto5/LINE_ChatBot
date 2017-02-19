<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/18
 * Time: 20:58
 */
use Symfony\Component\HttpFoundation\Request;
class LineRequestModel {

    private $request;
    private $eventType;
    private $replyToken;
    private $text;
    private $roomType;
    private $roomKey;

    /**
     * LineRequestModel constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $body = json_decode($request->getContent(), true);
        $this->eventType = $body["events"][0]["type"];
        $this->replyToken = $body["events"][0]["replyToken"];
        $this->text = $body["events"][0]["message"]["text"];
        $this->roomType = $body["events"][0]["source"]["type"];
        $this->roomKey = "";
        if ($this->roomType == "user") {
            $this->roomKey = $body["events"][0]["source"]["userId"];
        } else if ($this->roomType == "group") {
            $this->roomKey = $body["events"][0]["source"]["groupId"];
        }

        error_log("body      :".$request->getContent());
        error_log("eventType :".$this->eventType);
        error_log("replyToken:".$this->replyToken);
        error_log("text      :".$this->text);
        error_log("roomType  :".$this->roomType);
        error_log("roomKey   :".$this->roomKey);
    }

    /**
     * @return mixed
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @return mixed
     */
    public function getReplyToken()
    {
        return $this->replyToken;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getRoomType()
    {
        return $this->roomType;
    }

    /**
     * @return mixed
     */
    public function getRoomKey()
    {
        return $this->roomKey;
    }

}