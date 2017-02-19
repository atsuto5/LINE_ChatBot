<?php
require('../vendor/autoload.php');
require_once ('./lib/LineMessageUtil.php');
require_once ('./lib/LineClient.php');
require_once ('./lib/search/TokenModel.php');
require_once ('./lib/search/SearchModel.php');
require_once ('./lib/search/MessageModel.php');
require_once ('./lib/MemcacheUtil.php');
require_once ('./lib/MongoUtil.php');
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
    $lineRequestModel = new LineRequestModel($request);

    $memcacheUtil = new MemcacheUtil($lineRequestModel->getRoomKey());
    $mongoUtil = new MongoUtil();

    $tokenModel = new TokenModel($lineRequestModel->getText());
    $searchModel = new SearchModel($tokenModel,$lineRequestModel->getEventType());
    $messageModel = new MessageModel($searchModel);

    error_log(print_r($messageModel->getMessage(),true));
    error_log($memcacheUtil->get("wakeUp"));

    if ($searchModel->getOperation() == JOIN) { //joinのときは起きてるかどうかにかかわらず返信する。
        $lineClient->send($lineRequestModel->getReplyToken(), $messageModel->getMessage());
        return 'OK';
    }

    //コメントを保存する
    if ($memcacheUtil->get("comment")) {
        error_log("コメントを保存します");
        if ($lineRequestModel->getText() !== "完了") {
            $messages = $memcacheUtil->get("messages");
            if ($messages) {
                $messages[] = $lineRequestModel->getText();
            } else {
                $messages = array();
                $messages[] = $lineRequestModel->getText();
            }
            $memcacheUtil->set("messages",$messages,60);
        }

        //コメントを保存する
        if ($searchModel->getReservedMessageKey() == WRITE_COMPLETE_COMMENT) {
            $memcacheUtil->set("comment", false);
            $messages = $memcacheUtil->get("messages");
            $key = $memcacheUtil->get("comment_key");
            $comment = "";
            foreach ($messages as $message) {
                $comment .= $message."\n";
                $comment .= "ーーーーーー\n";
            }
            $mongoUtil->insertComment($lineRequestModel->getRoomKey(),$key,$comment);
        }
        return 'OK';
    }

    //コメントを書く
    if ($searchModel->getReservedMessageKey() == WRITE_COMMENT) {
        error_log("コメントを書きます。".$searchModel->getMaterials()[0]);
        $memcacheUtil->set("comment",true, 60);
        $memcacheUtil->set("comment_key",$searchModel->getMaterials()[0], 60);
    }

    if ($searchModel->getReservedMessageKey() == WAKEUP) {
        $memcacheUtil->set("wakeUp",true);
    }

    if($memcacheUtil->get("wakeUp")) {
        if ($messageModel->isResponseMessage()) {
            $lineClient->send($lineRequestModel->getReplyToken(), $messageModel->getMessage());
        }
    }

    if ($searchModel->getReservedMessageKey() == SLEEP) {
        $memcacheUtil->set("wakeUp",false);
    }

    return 'OK';
});

$app->run();
