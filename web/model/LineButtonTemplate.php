<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 20:28
 */
class LineButtonTemplate implements TemplateInterface {

    private $type = "buttons";
    private $thumbnailImageUrl;
    private $title;
    private $text;
    private $actions;

    public function __construct() {
        $this->thumbnailImageUrl = "";
        $this->title = "";
        $this->text = "";
        $this->actions = "";
    }


    public function getTemplateObject() {


    }
}