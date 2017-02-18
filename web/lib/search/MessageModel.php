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
require_once ('./model/UriTemplateAction.php');

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
			if (count($materials) == 0) {
				$this->setNoneMessage();
			} else if (count($materials) == 1) {
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

		error_log(print_r($this->materialDetail,true));
		$result = $this->materialDetail[$targetMaterial];
		error_log(print_r($result,true));

		if ($result) {
		    $buttonTemplate = new LineButtonTemplate();
		    $buttonTemplate->setThumbnailImageUrl($result["image_url"]);
		    $buttonTemplate->setTitle($result["name"]);

		    $postBackAction = new UriTemplateAction();
		    $postBackAction->setLabel("もっと詳しく");
            $postBackAction->setUri("https://host=shrouded-badlands-61521.herokuapp.com/uri_action_callback");

            $buttonTemplate->addAction($postBackAction);
            $this->messageArray[] = LineMessageUtil::getTemplateMessage("tes",$buttonTemplate);

			$message = <<<EOT
レベル　：{$result["level"]}
色　　　：{$result["color"]}
価格　　：{$result["price"]}
カテゴリ：{$result["category"]}
採取地　：{$result["price"]}
EOT;
		} else {
			$this->messageArray[] = LineMessageUtil::getTextMessage("ごめん。わからなかった...");
		}

		error_log(print_r($this->materialDetail,true));
    }

    private function setMultiMaterialMessage() {

		$message = "もしかして・・・\n";
		error_log(print_r($this->searchModel->getMaterials(),true));
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