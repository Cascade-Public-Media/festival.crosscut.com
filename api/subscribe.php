<?php
require __DIR__ . '/vendor/autoload.php';

use CrosscutFestival\Salesforce\Client;
use CrosscutFestival\Salesforce\Subscriber;
use Noodlehaus\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

$request = Request::createFromGlobals();

$mail = $request->get('mail');
if (empty($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    $response = new JsonResponse(
        ['error' => 'Invalid or empty email address.'],
        Response::HTTP_BAD_REQUEST
    );
    $response->send();
    die;
}

$config = new Config('config.json');

$client = new Client(
  $config->get('client.base_uri'),
  $config->get('client.id'),
  $config->get('client.secret'),
  $config->get('user.name'),
  $config->get('user.password'),
  $config->get('user.token')
);

$subscriber = new Subscriber($client, $mail, $config);
$subscriber->subscribe();

$response = new JsonResponse(['mail' => $mail]);
$response->send();
die;
