<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 21:43
 */

require ('./lib/search/DicConstant.php');

class SearchModel {

    private $tokenModel;

    private $operation;
    private $materials;
    private $searchLimit = 80;
    private $materialLimit = 60;
    /**
     * SearchModel constructor.
     * @param TokenModel $tokenModel
     */
    public function __construct($tokenModel) {
        $this->tokenModel = $tokenModel;
        $this->setOperation();
        $this->materials = array();

        if ($this->operation == "none") {
            return;
        } else if ($this->operation == "search") {
            $this->setMaterial();
        }
    }

    private function setOperation() {
        $tokens = $this->tokenModel->getToken();
        $reverse = array_reverse($tokens);
        foreach ($reverse as $token) {

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

            $this->operation = "none";
            return;
        }
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
                    $this->operation = "search";
                    return;
                }
            }
        }
    }


}