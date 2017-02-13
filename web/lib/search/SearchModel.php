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
    private $searchLimit = 80;
    private $materialLimit = 60;
    private $reservedLimit = 8.0;
    private $reservedMessageKey = false;
    /**
     * SearchModel constructor.
     * @param TokenModel $tokenModel
     */
    public function __construct($tokenModel) {
        $this->tokenModel = $tokenModel;

        if ($this->checkReservedWord()) {
            error_log($this->reservedMessageKey);
        }

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
				if(mb_strpos($noun->surface, $words["noun"],0, "UTF-8") !== false){
					error_log($noun->surface."と".$words["noun"]."が一致した");
					$checkNoun = true;
				}
			}

			foreach ($verbs as $verb) {
				error_log($verb->surface);
				error_log($words["verb"]);
				if(mb_strpos($verb->surface, $words["verb"],0, "UTF-8") !== false){
					error_log($verb->surface."と".$words["verb"]."が一致した");
					$checkVerb = true;
				}
			}

			//名詞のみの場合
			if ($words["verb"] == "") {
				if ($checkNoun) {
					return $key;
				}
			} else {
				if ($checkNoun && $checkVerb) {
					return $key;
				}
			}
		}
		return "0";
    }

    private function setOperation() {
        $tokens = $this->tokenModel->getToken();

        if (count($tokens) == 0) {
            $this->operation = "join";
            return;
        }

        $reverse = array_reverse($tokens);
        foreach ($reverse as $token) {

            error_log($token);
            //operation Search
            foreach (DicConstant::getSearchWords() as $word) {
                $result = 0;
                similar_text($token,$word,$result);
                error_log($token."と".$word."の類似度は".$result);

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
        foreach ($this->tokenModel->getToken() as $token) {

            if (strlen($token) <= 1) {
                continue;
            }

            //Material Search
            foreach (DicConstant::getMaterialWords() as $word) {
                $result = 0;
                similar_text($token,$word,$result);
                error_log($token."と".$word."の類似度は".$result);

                if ($result > $this->materialLimit) {
                    $this->materials[] = $token;
                }
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
     * @return bool
     */
    public function isReservedMessageKey()
    {
        return $this->reservedMessageKey;
    }



}