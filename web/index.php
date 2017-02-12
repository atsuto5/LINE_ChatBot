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

    $args = array(
        "channelId" => getenv("LINE_CHANNEL_ID"),
        "channelSecret" => getenv("LINE_CHANNEL_SECRET"),
        "channelMid" => getenv("LINE_CHANNEL_MID"),
        'proxy' => [
            'https' => getenv("FIXIE_URL"),
        ]
        );


    $httpClient = new \LINE\LINEBot\HTTPClient\GuzzleHTTPClient($args);
    $bot = new \LINE\LINEBot($args,$httpClient);
    $response = $bot->sendText("U8bc72504db9ad8b02ca8540ef86b8e41","test");
    error_log(json_encode($response));


    foreach ($body['events'] as $msg) {
        error_log(json_encode($msg));

        $resContent = $msg['message'];
        $resContent['text'] = 'ｶﾞｯ';

        $requestOptions = [
            'body' => json_encode([
                'to' => [$msg],
                'toChannel' => 1383378250, # Fixed value
                'eventType' => '138311608800106203', # Fixed value
                'content' => $resContent,
            ]),
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8',
                'X-Line-ChannelID' => getenv("LINE_CHANNEL_ID"),
                'X-Line-ChannelSecret' => getenv("LINE_CHANNEL_SECRET"),
                'X-Line-Trusted-User-With-ACL' => getenv("LINE_CHANNEL_MID"),
            ],
            'proxy' => [
                'https' => getenv("FIXIE_URL"),
            ],
        ];

        try {
            $client->post('https://trialbot-api.line.me/v1/events', $requestOptions);

        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    return 'OK';
});

$app->run();
