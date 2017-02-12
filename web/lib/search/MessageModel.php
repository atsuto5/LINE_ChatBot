<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 22:27
 */
require ('./lib/LineMessageUtil.php');
require ('./lib/search/DicConstant.php');

class MessageModel {

    private $searchModel;
    private $messageObject;

    /**
     * MessageModel constructor.
     * @param SearchModel $searchModel
     */
    public function __construct($searchModel) {
        $this->searchModel = $searchModel;
        $operation = $this->searchModel->getOperation();

        if ($operation == "none") {
            $this->setNoneMessage();
        } else if ($operation == "search") {
            $this->setSingleMaterialMessage();
        } else if ($operation == "join") {
            $this->setJoinedMessage();
        } else {
            $this->setNoneMessage();
        }
    }

    public function getMessage() {
        return $this->messageObject;
    }

    private function setSingleMaterialMessage() {
        $this->messageObject = LineMessageUtil::getTextMessage("検索する予定です");
    }

    private function setMultiMaterialMessage() {
        $this->messageObject = LineMessageUtil::getTextMessage("検索する予定です");
    }

    private function setNoneMessage() {
        $noneMessages = DicConstant::getNoneMessages();
        srand((float) microtime() * 10000000);
        $rand_keys = array_rand($noneMessages, 1);

        $this->messageObject = LineMessageUtil::getTextMessage($rand_keys[0]);
    }

    private function setJoinedMessage() {
        $message = <<<EOT
追加ありがとう！！
錬金術とキルヘン・ベルのことなら教えられるよ！
わからないことがあったら、なんでも聞いてね！
EOT;
         $this->messageObject = LineMessageUtil::getTextMessage($message);
    }


}