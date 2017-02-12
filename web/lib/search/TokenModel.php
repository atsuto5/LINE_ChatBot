<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 21:32
 */

require ('../Igo.php');

class TokenModel {

    private $originText;
    private $token;
    private $igo;

    public function __construct($text) {
        $this->originText = $text;
        $this->igo = new Igo("../Igo/ipadic", "UTF-8");
        $this->token = $this->igo->wakati($text);
    }

    /**
     * @return mixed
     */
    public function getOriginText(){
        return $this->originText;
    }

    /**
     * @return 分かち書きされた文字列のリスト
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @return Igo
     */
    public function getIgo() {
        return $this->igo;
    }




}