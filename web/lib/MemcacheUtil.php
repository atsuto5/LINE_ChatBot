<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/18
 * Time: 18:55
 */
use MemCachier\MemcacheSASL;

class MemcacheUtil {

    private $memcache;

    public function __construct() {
        // Create client
        $this->memcache = new MemcacheSASL();
        $servers = explode(",", getenv("MEMCACHIER_SERVERS"));
        foreach ($servers as $s) {
            $parts = explode(":", $s);
            $this->memcache->addServer($parts[0], $parts[1]);
        }

        // Setup authentication
        $this->memcache->setSaslAuthData( getenv("MEMCACHIER_USERNAME")
            , getenv("MEMCACHIER_PASSWORD") );
    }

    /**
     * @param string $key
     * @param array|bool|float|int|mixed|string $value
     */
    public function add($key,$value) {
        $this->memcache->add($key,$value);
    }

    /**
     * @param $key
     * @return array|bool|float|int|mixed|string
     */
    public function get($key) {
        return $this->memcache->get($key);
    }

}