<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 20:35
 */
class PostBackTemplateAction implements TemplateActionInterface {
    private $type = "postback";
    private $label;
    private $data;
    private $text;

    public function __construct() {
        $this->label = "";
        $this->data = "";
        $this->text = "";
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
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }


    /**
     * @return array
     */
    public function getAction()
    {
        return array(
            "type" => $this->type,
            "label" => $this->label,
            "data" => $this->data,
            "text" => $this->text
        );
    }
}