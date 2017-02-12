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
    error_log($body);

    foreach ($body['result'] as $msg) {
        if (!preg_match('/(ぬるぽ|ヌルポ|ﾇﾙﾎﾟ|nullpo)/i', $msg['content']['text'])) {
            continue;
        }

        $resContent = $msg['content'];
        $resContent['text'] = 'ｶﾞｯ';

        $requestOptions = [
            'body' => json_encode([
                'to' => [$msg['content']['from']],
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
