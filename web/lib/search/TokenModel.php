<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 21:32
 */

require_once ('./lib/Igo.php');

class TokenModel {

    private $originText;
    private $token;
    private $igo;

    public function __construct($text) {
        $this->originText = $text;
        $this->igo = new Igo("./lib/Igo/ipadic", "UTF-8");
        $this->token = $this->igo->parse($text);
		error_log(print_r($this->token,true));
    }

    /**
     * @return mixed
     */
    public function getOriginText(){
        return $this->originText;
    }

    /**
     * @return array 解析結果の形態素リスト
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