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
    /**
     * SearchModel constructor.
     * @param TokenModel $tokenModel
     */
    public function __construct($tokenModel) {
        $this->tokenModel = $tokenModel;
        $this->getOperation();
    }

    private function getOperation() {
        foreach ($this->tokenModel->getToken() as $token) {

            foreach (DicConstant::getSearchWords() as $word) {
                $result = 0;
                similar_text($token,$word,$result);
                error_log($token."と".$word."の類似度は".$result);
            }
        }
    }



}