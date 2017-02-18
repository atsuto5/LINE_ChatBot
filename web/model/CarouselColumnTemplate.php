<?php
require_once ('./model/TemplateInterface.php');
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/18
 * Time: 15:21
 */
class CarouselColumnTemplate implements TemplateInterface {

    private $thumbnailImageUrl;
    private $title;
    private $text;
    private $actions;

    public function __construct() {
        $this->thumbnailImageUrl = "";
        $this->title = "";
        $this->text = "default";
        $this->actions = array();
    }

    /**
     * @return string
     */
    public function getThumbnailImageUrl()
    {
        return $this->thumbnailImageUrl;
    }

    /**
     * @param string $thumbnailImageUrl
     */
    public function setThumbnailImageUrl($thumbnailImageUrl)
    {
        $this->thumbnailImageUrl = $thumbnailImageUrl;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param TemplateActionInterface $actions
     */
    public function addAction(TemplateActionInterface $actions)
    {
        $this->actions[] = $actions;
    }

    public function getTemplateObject() {

        $actionArray = array();
        foreach ($this->actions as $action) {
            $actionArray[] = $action->getAction();
        }

        return array(
            "thumbnailImageUrl" => $this->thumbnailImageUrl,
            "title" => $this->title,
            "text" => $this->text,
            "actions" => $actionArray
        );
    }
}