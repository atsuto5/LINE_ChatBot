<?php

require('../vendor/autoload.php');
require ('./lib/LineMessageUtil.php');
require ('./lib/LineClient.php');
require ('./lib/search/TokenModel.php');
require ('./lib/search/SearchModel.php');

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
    $body = json_decode($request->getContent(), true);
    error_log($request->getContent());

    $eventType = $body["events"][0]["type"];
    $replyToken = $body["events"][0]["replyToken"];
    $text = $body["events"][0]["message"]["text"];

    error_log("eventType ",$eventType);
    if ($eventType == "join") {
        $lineClient->send($replyToken,[LineMessageUtil::getTextMessage("追加ありがとう！！")])

    } else {
        $tokenModel = new TokenModel($text);
        $searchModel = new SearchModel($tokenModel);

        error_log($replyToken);
        error_log($text);
        error_log(json_encode(LineMessageUtil::getTextMessage("tes")));

        $responseText = LineMessageUtil::getTextMessage("tes");

        $responseImage = LineMessageUtil::getImageMessage("https://shrouded-badlands-61521.herokuapp.com/images/lang-logo.png","https://shrouded-badlands-61521.herokuapp.com/images/lang-logo.png");

        $responseVideo = LineMessageUtil::getVideoMessage("https://www.youtube.com/watch?v=zW279TqmDFE","https://shrouded-badlands-61521.herokuapp.com/images/favicon.png");

        $responseSticker = LineMessageUtil::getStickerMessage("2","522");


        $lineClient->send($replyToken,[$responseText]);
    }



    return 'OK';
});

$app->run();
