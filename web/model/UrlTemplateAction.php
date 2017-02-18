<?php
require_once ('./model/TemplateActionInterface.php');
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/18
 * Time: 13:48
 */
class UrlTemplateAction implements TemplateActionInterface {
    private $type = "url";
    private $label;
    private $uri;

    public function __construct() {
        $this->label = "";
        $this->label = "";
        $this->uri = "";
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return array
     */
    public function getAction(){
        return array(
            "type" => $this->type,
            "label" => $this->label,
            "uri" => $this->uri
        );
    }
}