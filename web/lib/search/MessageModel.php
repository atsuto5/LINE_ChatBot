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
                    $this->setNoneMessage();
                } else if (count($materials) == 1) {
                    $this->setSingleMaterialMessage();
                } else {
                    $this->setMultiMaterialMessage();
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

		    $postBackAction = new PostBackTemplateAction();
		    $postBackAction->setLabel("もっと詳しく");
            $postBackAction->setData($result["name"]);
            $postBackAction->setText($result["name"]."をもっと詳しく教えて");

            $buttonTemplate->addAction($postBackAction);
            $this->messageArray[] = LineMessageUtil::getTemplateMessage($result["name"],$buttonTemplate);
		} else {
			$this->messageArray[] = LineMessageUtil::getTextMessage("ごめん。わからなかった...");
		}

		error_log(print_r($this->materialDetail,true));
    }

    private function setMultiMaterialMessage() {

        $carouselTemplate = new LineCarouselTemplate();
		foreach ($this->searchModel->getMaterials() as $material) {
            $detail = $this->materialDetail[$material];

            $columnTemplate = new CarouselColumnTemplate();
            $columnTemplate->setThumbnailImageUrl($detail["image_url"]);
            $columnTemplate->setTitle($detail["name"]);

            $postBackAction = new PostBackTemplateAction();
            $postBackAction->setLabel("もっと詳しく");
            $postBackAction->setData("data");

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
        foreach (DicConstant::getPriceWords() as $word) {
            $message .= "{$word}\n";
        }

        $this->messageArray[] = LineMessageUtil::getTextMessage($message);
    }

    private function setSearchDetailMessage() {
        $targetMaterial = $this->searchModel->getMaterials()[0];
        $detail = $this->materialDetail[$targetMaterial];

        $message  = $detail["name"]."の詳細だよ！！\n";
        $message  = "ーーーーーーー\n";
        $message .= "名前　　：".$detail["name"]."\n";
        $message .= "レベル　：".$detail["level"]."\n";
        $message .= "色　　　：".$detail["color"]."\n";
        $message .= "カテゴリ：".$detail["category"]."\n";
        $message .= "価格　　：".$detail["price"]."\n";
        $message .= "店　　　：".$detail["shop"];
        $message  = "ーーーーーーー\n";
        $message .= "採取地　：".$detail["place"]."\n";
        $message .= "ドロップ：".$detail["monster"]."\n";
        $message  = "ーーーーーーー\n";
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