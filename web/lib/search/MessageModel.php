<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 22:27
 */
class MessageModel {

    private $searchModel;

    /**
     * MessageModel constructor.
     * @param SearchModel $searchModel
     */
    public function __construct($searchModel) {
        $this->searchModel = $searchModel;
    }

    public function getSingleMaterialMessage() {

    }

    public function getMultiMaterialMessage() {

    }


}