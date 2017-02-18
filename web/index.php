<?php
require('../vendor/autoload.php');
require_once ('./lib/LineMessageUtil.php');
require_once ('./lib/LineClient.php');
require_once ('./lib/search/TokenModel.php');
require_once ('./lib/search/SearchModel.php');
require_once ('./lib/search/MessageModel.php');

use Symfony\Component\HttpFoundation\Request;
use MemCachier\MemcacheSASL;

date_default_timezone_set("Asia/Tokyo");

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

$app->post('/callback', function (Request $request) use ($app) {

    $lineClient = new LineClient();
    $body = json_decode($request->getContent(), true);
    error_log($request->getContent());

    $eventType = $body["events"][0]["type"];
    $replyToken = $body["events"][0]["replyToken"];
    $text = $body["events"][0]["message"]["text"];

    $tokenModel = new TokenModel($text);
    $searchModel = new SearchModel($tokenModel,$eventType);
    $messageModel = new MessageModel($searchModel);

    error_log("eventType ".$eventType);
    error_log("replyToken ".$replyToken);
    error_log("text ".$text);
    error_log(print_r($messageModel->getMessage(),true));

    if ($messageModel->isReturnMessage()) {
        $lineClient->send($replyToken,$messageModel->getMessage());
    }

    // Create client
    $m = new MemcacheSASL();
    $servers = explode(",", getenv("MEMCACHIER_SERVERS"));
    foreach ($servers as $s) {
        $parts = explode(":", $s);
        $m->addServer($parts[0], $parts[1]);
    }

    // Setup authentication
    $m->setSaslAuthData( getenv("MEMCACHIER_USERNAME")
        , getenv("MEMCACHIER_PASSWORD") );

    // Test client
    $m->add("foo", "bar");
    echo $m->get("foo");

    return 'OK';
});

$app->run();
