<?php

require('../vendor/autoload.php');
require ('./lib/LineMessageUtil.php');

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

    $body = json_decode($request->getContent(), true);
    error_log($request->getContent());

    $accessToken = getenv("LINE_ACCESS_TOKEN");

    $replyToken = $body["events"][0]["replyToken"];
    $text = $body["events"][0]["message"]["text"];

    error_log($replyToken);
    error_log($text);
    error_log(json_encode(LineMessageUtil::getTextMessage("tes")));

    $responseText = LineMessageUtil::getTextMessage("tes");

    $responseImage = LineMessageUtil::getImageMessage("https://shrouded-badlands-61521.herokuapp.com/images/lang-logo.png","https://shrouded-badlands-61521.herokuapp.com/images/lang-logo.png");

    $postData = [
        "replyToken" => $replyToken,
        "messages" => [$responseText,$responseImage]
    ];

    $ch = curl_init("https://api.line.me/v2/bot/message/reply");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charser=UTF-8',
        'Authorization: Bearer ' . $accessToken
    ));
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
    curl_setopt($ch, CURLOPT_PROXYPORT, '80');
    curl_setopt($ch, CURLOPT_PROXY, getenv("FIXIE_URL_ONLY"));
    $result = curl_exec($ch);
    error_log(json_encode($result));
    curl_close($ch);

    return 'OK';
});

$app->run();
