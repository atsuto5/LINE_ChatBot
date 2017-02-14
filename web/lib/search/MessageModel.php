<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 22:27
 */
require_once ('./lib/LineMessageUtil.php');
require_once ('./lib/search/DicConstant.php');

class MessageModel {

    private $searchModel;
    private $messageArray;
	private $materialDetail;

    /**
     * MessageModel constructor.
     * @param SearchModel $searchModel
     */
    public function __construct($searchModel) {
        $this->searchModel = $searchModel;
		$this->materialDetail = json_decode(file_get_contents("./data/material.json"),true);
		$this->messageArray = array();
        $operation = $this->searchModel->getOperation();

        if ($operation == "none") {
            $this->setNoneMessage();
        } else if ($operation == "search") {
			$materials = $this->searchModel->getMaterials();
			if (count($materials) == 1) {
				$this->setSingleMaterialMessage();
			} else {
				$this->setMultiMaterialMessage();
			}
        } else if ($operation == "join") {
            $this->setJoinedMessage();
        } else if ($operation == "reserve"){
			$this->setReservedMessage();
		} else {
            $this->setNoneMessage();
        }
    }

    public function getMessage() {
        return $this->messageArray;
    }

    private function setSingleMaterialMessage() {
		$targetMaterial = $this->searchModel->getMaterials()[0];
        $this->messageArray[] = LineMessageUtil::getTextMessage($targetMaterial."を探してくるよ！！");

		error_log(print_r($this->materialDetail,true));

		$result = $this->materialDetail[$targetMaterial];
		error_log(print_r($result,true));

		if ($result) {
			$this->messageArray[] = LineMessageUtil::getImageMessage($result["image_url"],$result["image_url"]);
			$message = <<<EOT
レベル　：{$result["level"]}
色　　　：{$result["color"]}
価格　　：{$result["price"]}
カテゴリ：{$result["category"]}
採取地　：{$result["price"]}
こんな感じだよ！！！
EOT;
			$this->messageArray[] = LineMessageUtil::getTextMessage($message);

		} else {
			$this->messageArray[] = LineMessageUtil::getTextMessage("ごめん。わからなかった...");
		}

		error_log(print_r($this->materialDetail,true));
    }

    private function setMultiMaterialMessage() {

		$message = "もしかして・・・\n";
		foreach ($this->searchModel->getMaterials() as $material) {
			$message .= $material;
		}
		$message .= "のこと？";

		$this->messageArray[] = LineMessageUtil::getTextMessage($message);
    }

    private function setNoneMessage() {
        $noneMessages = DicConstant::getNoneMessages();
        shuffle($noneMessages);
		$this->messageArray[] = LineMessageUtil::getTextMessage($noneMessages[0]);
    }

    private function setReservedMessage() {
		if ($this->searchModel->getReservedMessageKey() == "1" ||
			$this->searchModel->getReservedMessageKey() == "2"
		) {
			$message = <<<EOT
ヘルプだよ！！
EOT;
			$this->messageArray[] = LineMessageUtil::getTextMessage($message);

		}

	}

    private function setJoinedMessage() {
        $message = <<<EOT
追加ありがとう！！
錬金術とキルヘン・ベルのことなら教えられるよ！
わからないことがあったら、なんでも聞いてね！
EOT;
		$this->messageArray[] = LineMessageUtil::getTextMessage($message);
    }


}