<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 22:27
 */
require_once ('./lib/LineMessageUtil.php');
require_once ('./lib/search/DicConstant.php');
require_once ('./model/LineButtonTemplate.php');
require_once ('./model/LineCarouselTemplate.php');
require_once ('./model/CarouselColumnTemplate.php');
require_once ('./model/PostBackTemplateAction.php');
require_once ('./lib/MongoUtil.php');

class MessageModel {

    private $searchModel;
    private $messageArray;
	private $materialDetail;
	private $isResponseMessage;

    /**
     * MessageModel constructor.
     * @param SearchModel $searchModel
     */
    public function __construct($searchModel) {
        $this->searchModel = $searchModel;
		$this->materialDetail = json_decode(file_get_contents("./data/material.json"),true);
		$this->messageArray = array();
		$this->isResponseMessage = true;
        $operation = $this->searchModel->getOperation();

        switch ($operation) {
            case NONE:
                $this->setNoneMessage();
                break;
            case SEARCH:
                $materials = $this->searchModel->getMaterials();
                if (count($materials) == 0) {
                    $similarMaterials = $this->searchModel->getSimilarMaterials();
                    error_log(print_r($similarMaterials,true));
                    if (count($similarMaterials) == 0) {
                        $this->setNoneMessage();
                    } else {
                        $this->setMultiMaterialMessage();
                    }
                } else if (count($materials) == 1) {
                    $this->setSingleMaterialMessage();
                }
                break;
            case JOIN:
                $this->setJoinedMessage();
                break;
            case RESERVE :
                $this->setReservedMessage();
                break;
            case POSTBACK :
                $this->setIsResponseMessage(false);
                break;
            default :
                $this->setNoneMessage();
                break;
        }
    }

    public function getMessage() {
        return $this->messageArray;
    }

    private function setSingleMaterialMessage() {
		$targetMaterial = $this->searchModel->getMaterials()[0];

		error_log(print_r($this->materialDetail,true));
		$result = $this->materialDetail[$targetMaterial];
		error_log(print_r($result,true));

		if ($result) {
            $message = <<<EOT
カテゴリ：{$result["category"]}
EOT;
		    $buttonTemplate = new LineButtonTemplate();
		    $buttonTemplate->setThumbnailImageUrl($result["image_url"]);
		    $buttonTemplate->setTitle($result["name"]);
		    $buttonTemplate->setText($message);

		    $detailAction = new PostBackTemplateAction();
            $detailAction->setLabel("もっと詳しく");
            $detailAction->setData($result["name"]);
            $detailAction->setText($result["name"]."をもっと詳しく教えて");

            $commentReadAction = new PostBackTemplateAction();
            $commentReadAction->setLabel("コメントをみる");
            $commentReadAction->setData($result["name"]);
            $commentReadAction->setText($result["name"]."のコメントをみる");

            $commentWriteAction = new PostBackTemplateAction();
            $commentWriteAction->setLabel("コメントを書く");
            $commentWriteAction->setData($result["name"]);
            $commentWriteAction->setText($result["name"]."のコメントを書く");

            $buttonTemplate->addAction($detailAction);
            $buttonTemplate->addAction($commentReadAction);
            $buttonTemplate->addAction($commentWriteAction);
            $this->messageArray[] = LineMessageUtil::getTemplateMessage($result["name"],$buttonTemplate);
		} else {
			$this->messageArray[] = LineMessageUtil::getTextMessage("ごめん。わからなかった...");
		}

		error_log(print_r($this->materialDetail,true));
    }

    private function setMultiMaterialMessage() {

        $carouselTemplate = new LineCarouselTemplate();
		foreach ($this->searchModel->getSimilarMaterials() as $material) {
            $detail = $this->materialDetail[$material];

            $message = <<<EOT
カテゴリ：{$detail["category"]}
EOT;
            $columnTemplate = new CarouselColumnTemplate();
            $columnTemplate->setThumbnailImageUrl($detail["image_url"]);
            $columnTemplate->setTitle($detail["name"]);
            $columnTemplate->setText($message);

            $postBackAction = new PostBackTemplateAction();
            $postBackAction->setLabel("もっと詳しく");
            $postBackAction->setData($detail["name"]);
            $postBackAction->setText($detail["name"]."をもっと詳しく教えて");

            $columnTemplate->addAction($postBackAction);
            $carouselTemplate->addColumn($columnTemplate);
		}

        $this->messageArray[] = LineMessageUtil::getTemplateMessage("tes",$carouselTemplate);
    }

    private function setReservedMessage()
    {
        switch ($this->searchModel->getReservedMessageKey()) {
            case HELP :
                $this->setHelpMessage();
                break;
            case SEARCH_DETAIL:
                $this->setSearchDetailMessage();
                break;
            case WAKEUP:
                $this->setWakeUpMessage();
                break;
            case SLEEP:
                $this->setSleepMessage();
                break;
            case READ_COMMENT:
                $this->setReadCommentMessage();
                break;
            case WRITE_COMMENT:
                $this->setWriteCommentMessage();
                break;
        }
    }

    private function setHelpMessage() {
        $message = <<<EOT
私はこんなことがわかるよ！

EOT;
        $message .= "[素材]\n";
        foreach (DicConstant::getMaterialWords() as $word) {
            $message .= "{$word}\n";
        }

        $message .= "\n";
        $message .= "[場所]\n";
        foreach (DicConstant::getPlaceWords() as $word) {
            $message .= "{$word}\n";
        }

        $this->messageArray[] = LineMessageUtil::getTextMessage($message);
    }

    private function setSearchDetailMessage() {
        $targetMaterial = $this->searchModel->getMaterials()[0];
        $detail = $this->materialDetail[$targetMaterial];

        $message  = $detail["name"]."の詳細だよ！！\n";
        $message .= "ーーーーーーー\n";
        $message .= "名前　　：".$detail["name"]."\n";
        $message .= "レベル　：".$detail["level"]."\n";
        $message .= "色　　　：".$detail["color"]."\n";
        $message .= "カテゴリ：".$detail["category"]."\n";
        $message .= "価格　　：".$detail["price"]."\n";
        $message .= "店　　　：".$detail["shop"]."\n";
        $message .= "ーーーーーーー\n";
        $message .= "採取地　：".$detail["place"]."\n";
        $message .= "ドロップ：".$detail["monster"]."\n";
        $message .= "ーーーーーーー";
        $this->messageArray[] = LineMessageUtil::getTextMessage($message);
    }

    private function setNoneMessage() {
        $noneMessages = DicConstant::getNoneMessages();
        shuffle($noneMessages);
        $this->messageArray[] = LineMessageUtil::getTextMessage($noneMessages[0]);
    }

    private function setWakeUpMessage() {
        $wakeUpMessages = DicConstant::getWakeUpMessages();
        shuffle($wakeUpMessages);
        $this->messageArray[] = LineMessageUtil::getTextMessage($wakeUpMessages[0]);
    }

    private function setSleepMessage() {
        $sleepMessages = DicConstant::getSleepMessages();
        shuffle($sleepMessages);
        $this->messageArray[] = LineMessageUtil::getTextMessage($sleepMessages[0]);
    }

    private function setReadCommentMessage() {
        $this->messageArray[] = LineMessageUtil::getTextMessage("コメント読んでね");
    }

    private function setWriteCommentMessage() {
        $targetMaterial = $this->searchModel->getMaterials()[0];
        $detail = $this->materialDetail[$targetMaterial];

        $message = $detail["name"]."のコメント書いてね\n";
        $message .= "今から１分以内にメッセージを書いて、最後に「完了」って入力すると登録できるよ！\n";
        $message .= "ではでは、スタート！";

        $this->messageArray[] = LineMessageUtil::getTextMessage($message);
    }

    private function setJoinedMessage() {
        $message = <<<EOT
追加ありがとう！！
錬金術とキルヘン・ベルのことなら教えられるよ！
わからないことがあったら、なんでも聞いてね！
EOT;
		$this->messageArray[] = LineMessageUtil::getTextMessage($message);
    }

    /**
     * @param bool $isResponseMessage
     */
    public function setIsResponseMessage($isResponseMessage)
    {
        $this->isResponseMessage = $isResponseMessage;
    }

    /**
     * @return bool
     */
    public function isResponseMessage()
    {
        return $this->isResponseMessage;
    }
}