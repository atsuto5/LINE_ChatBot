<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/19
 * Time: 12:19
 */
use MongoDB\Client;
class MongoUtil {
    private $client;
    private $commentCollection;

    public function __construct() {
        // Create client
        $this->client = new Client(getenv("MONGODB_URI"));
        $this->commentCollection = $this->client->selectCollection("heroku_917cpv07","comment");
    }

    public function insertComment($userId,$key,$comment) {
        $this->commentCollection->insertOne(
            array (
                "userId" => $userId,
                "key" => $key,
                "comment" => $comment,
                "create_time" => strtotime("now")
            )
        );
    }

    public function countComment($key) {
        return $this->commentCollection->count(array("key" => $key));
    }

    public function findComment($key,$limit = 3) {
        $cursor = $this->commentCollection->find(
            array(
                "key" => $key
            ),
            array(
                "limit" => $limit,
                "sort" => array ("create_time" => -1)
            )
        );
        return $cursor->toArray();
    }

}