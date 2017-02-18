<?php
ini_set('session.save_handler', 'memcached');
ini_set('session.save_path', 'PERSISTENT=pool ' . getenv('MEMCACHIER_SERVERS'));
ini_set('memcached.sess_binary', 1);
ini_set('memcached.sess_sasl_username', getenv('MEMCACHIER_USERNAME'));
ini_set('memcached.sess_sasl_password', getenv('MEMCACHIER_PASSWORD'));

require('../vendor/autoload.php');
require_once ('./lib/LineMessageUtil.php');
require_once ('./lib/LineClient.php');
require_once ('./lib/search/TokenModel.php');
require_once ('./lib/search/SearchModel.php');
require_once ('./lib/search/MessageModel.php');

use Symfony\Component\HttpFoundation\Request;

date_default_timezone_set("Asia/Tokyo");
session_start();

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

    error_log($_SESSION["te"]);

    $_SESSION["te"] = "いいい";

    return 'OK';
});

$app->run();
