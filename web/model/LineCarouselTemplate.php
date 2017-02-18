<?php
require_once ('./model/TemplateInterface.php');
require_once ('./model/LineButtonTemplate.php');
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 20:28
 */
class LineCarouselTemplate implements TemplateInterface {

    private $type = "carousel";
    private $columns;

    public function __construct(){
        $this->columns = array();
    }

    public function addColumn(LineButtonTemplate $buttonTemplate) {
        $this->columns[] = $buttonTemplate;

        if (count($this->columns) > 5) {
            array_shift($this->columns);
        }
    }

    public function getTemplateObject() {
        $columnArray = array();
        foreach ($this->columns as $column) {
            $templateObject = $column->getTemplateObject();
            $entry = array(
                "thumbnailImageUrl" => $templateObject["thumbnailImageUrl"],
                "title" => $templateObject["title"],
                "text" => $templateObject["text"],
                "actions"=> $templateObject["actions"]
            );
            $columnArray[] = $entry;
        }

        return array(
            "type" => $this->type,
            "columns" => $columnArray
        );
    }
}