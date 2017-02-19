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
    private $roomKey;

    public function __construct($key) {
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
        $this->roomKey = $key;
    }

    /**
     * @param string $key
     * @param array|bool|float|int|mixed|string $value
     */
    public function add($key,$value) {
        $this->memcache->add($this->roomKey."_".$key,$value);
    }

    /**
     * @param string $key
     * @param array|bool|float|int|mixed|string $value
     * @param int $expire
     */
    public function set($key,$value,$expire = 0) {
        $this->memcache->set($this->roomKey."_".$key,$value,$expire);
    }

    /**
     * @param $key
     * @return array|bool|float|int|mixed|string
     */
    public function get($key) {
        return $this->memcache->get($this->roomKey."_".$key);
    }
}