<?php

require('../vendor/autoload.php');

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
    $client = new GuzzleHttp\Client();

    $body = json_decode($request->getContent(), true);
    error_log($request->getContent());

    $accessToken = "xXNBySf4S9RO1vIkD1RfCjKPn1nA+UCc9fybgJXhixe8k4ZOFrP7kTaG7ADNhTaglrHx48RtsZ+s7lSoS8/tTLOVV7bxQO5jQae+ohgmRBaonHf5gUTkmUVRQm8TZKAj8PnOnn6vfwqcmJvGbszWiwdB04t89/1O/w1cDnyilFU=";

    $replyToken = $body["events"][0]["replyToken"];
    $text = $body["events"][0]["message"]["text"];

    error_log($replyToken);
    error_log($text);

    $responseText = [
        "type" => "text",
        "text" => "てええう"
    ];

    $postData = [
        "replyToken" => $replyToken,
        "messages" => [$responseText]
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
    curl_setopt($ch, CURLOPT_PROXY, 'http://fixie:hMiDdbvvZHgMotg@velodrome.usefixie.com');
    $result = curl_exec($ch);
    curl_close($ch);





//    foreach ($body['events'] as $msg) {
//        error_log(json_encode($msg));
//
//        $resContent = $msg['message'];
//        $resContent['text'] = 'ｶﾞｯ';
//
//        $requestOptions = [
//            'body' => json_encode([
//                'to' => [$msg],
//                'toChannel' => 1383378250, # Fixed value
//                'eventType' => '138311608800106203', # Fixed value
//                'content' => $resContent,
//            ]),
//            'headers' => [
//                'Content-Type' => 'application/json; charset=UTF-8',
//                'X-Line-ChannelID' => getenv("LINE_CHANNEL_ID"),
//                'X-Line-ChannelSecret' => getenv("LINE_CHANNEL_SECRET"),
//                'X-Line-Trusted-User-With-ACL' => getenv("LINE_CHANNEL_MID")
//            ],
//            'proxy' => [
//                'https' => getenv("FIXIE_URL"),
//            ],
//        ];
//
//        try {
//            $client->post('https://trialbot-api.line.me/v1/events', $requestOptions);
//
//        } catch (Exception $e) {
//            error_log($e->getMessage());
//        }
//    }

    return 'OK';
});

$app->run();
