<?php
require('../vendor/autoload.php');
require_once ('./lib/LineMessageUtil.php');
require_once ('./lib/LineClient.php');
require_once ('./lib/search/TokenModel.php');
require_once ('./lib/search/SearchModel.php');
require_once ('./lib/search/MessageModel.php');
require_once ('./lib/MemcacheUtil.php');
require_once ('./model/LineRequestModel.php');

use Symfony\Component\HttpFoundation\Request;

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
    error_log(print_r($request,true));

    $body = json_decode($request->getContent(), true);
    error_log($request->getContent());

    $eventType = $body["events"][0]["type"];
    $replyToken = $body["events"][0]["replyToken"];
    $text = $body["events"][0]["message"]["text"];
    $roomType = $body["events"][0]["source"]["type"];
    $key = "";
    if ($roomType == "user") {
        $key = $body["events"][0]["source"]["userId"];
    } else if ($roomType == "group") {
        $key = $body["events"][0]["source"]["groupId"];
    }

    $memcacheUtil = new MemcacheUtil($key);

    $tokenModel = new TokenModel($text);
    $searchModel = new SearchModel($tokenModel,$eventType);
    $messageModel = new MessageModel($searchModel);

    error_log("eventType ".$eventType);
    error_log("replyToken ".$replyToken);
    error_log("text ".$text);
    error_log(print_r($messageModel->getMessage(),true));
    error_log($memcacheUtil->get("wakeUp"));

    if ($searchModel->getOperation() == "join") { //joinのときは起きてるかどうかにかかわらず返信する。
        $lineClient->send($replyToken, $messageModel->getMessage());
        return 'OK';
    }

    if ($searchModel->getReservedMessageKey() == "3") { //wakeUp
        $memcacheUtil->set("wakeUp",true);
    }

    if($memcacheUtil->get("wakeUp")) {
        if ($messageModel->isResponseMessage()) {
            $lineClient->send($replyToken, $messageModel->getMessage());
        }
    }

    if ($searchModel->getReservedMessageKey() == "4") { //sleep
        $memcacheUtil->set("wakeUp",false);
    }

    return 'OK';
});

$app->run();
