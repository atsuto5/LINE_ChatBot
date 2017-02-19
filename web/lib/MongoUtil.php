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

}