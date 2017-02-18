<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 21:43
 */

require_once ('./lib/search/DicConstant.php');

class SearchModel {

    private $tokenModel;

    private $operation;
    private $materials;
    private $eventType;
    private $searchLimit = 80;
    private $materialLimit = 60;
    private $reservedLimit = 8.0;
    private $reservedMessageKey = false;
    /**
     * SearchModel constructor.
     * @param TokenModel $tokenModel
     * @param string $eventType
     */
    public function __construct($tokenModel,$eventType = null) {
        $this->tokenModel = $tokenModel;
        $this->eventType = $eventType;

        $this->setOperation();
        $this->materials = array();

        error_log($this->operation);
        if ($this->operation == "none") {
            return;
        } else if ($this->operation == "search") {
            $this->setMaterial();
        }
    }

    private function checkReservedWord() {

		$verbs = $this->tokenModel->getVerbs();
		$nouns = $this->tokenModel->getNouns();

		foreach (DicConstant::getReservedWords() as $key => $words) {

			$checkNoun = false;
			$checkVerb = false;
			foreach ($nouns as $noun) {
				if(mb_strpos($words["noun"], $noun->surface,0, "UTF-8") !== false){
					error_log($noun->surface."と".$words["noun"]."が一致した");
					$checkNoun = true;
				}
			}

			foreach ($verbs as $verb) {
				error_log($verb->surface);
				error_log($words["verb"]);
				if(mb_strpos($words["verb"], $verb->surface ,0, "UTF-8") !== false){
					error_log($verb->surface."と".$words["verb"]."が一致した");
					$checkVerb = true;
				}
			}

			//名詞のみの場合
			if ($words["verb"] == "") {
				if ($checkNoun) {
					$this->reservedMessageKey = $key;
					return true;
				}
			} else {
				if ($checkNoun && $checkVerb) {
					$this->reservedMessageKey = $key;
					return true;
				}
			}
		}
		return false;
    }

    private function setOperation() {
        $tokens = $this->tokenModel->getToken();

        if (!is_null($this->eventType)) {
            switch ($this->eventType) {
                case "join":
                    $this->operation = "join";
                    return;
                case "postback":
                    $this->operation = "postback";
                    return;
            }
        }

        if ($this->checkReservedWord()) {
            $this->operation = "reserve";
            return;
        }

        $reverse = array_reverse($tokens);
        foreach ($reverse as $token) {
            error_log(print_r($token,true));
            //operation Search
            foreach (DicConstant::getSearchWords() as $word) {
                $result = 0;
                similar_text($token->surface,$word,$result);
                error_log($token->surface."と".$word."の類似度は".$result);

                if ($result > $this->searchLimit) {
                    $this->operation = "search";
                    return;
                }
            }
        }

        $this->operation = "none";
        return;
    }

    private function setMaterial() {
		foreach (DicConstant::getMaterialWords() as $word) {
			$result = 0;
			$nounsText = $this->tokenModel->getInVerbsText();
			similar_text($nounsText,$word,$result);

			error_log($nounsText."と".$word."の類似度は".$result);

			if ($result > $this->materialLimit) {
				$this->materials[] =$word;
			}
		}
    }

    /**
     * @return mixed
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @return array
     */
    public function getMaterials()
    {
        return $this->materials;
    }

	/**
	 * @return string
	 */
    public function getReservedMessageKey()
    {
        return $this->reservedMessageKey;
    }



}