<?php
require __DIR__ . '/vendor/autoload.php';

use CrosscutFestival\Salesforce\Client;
use CrosscutFestival\Salesforce\Subscriber;
use Dotenv\Dotenv;
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

$dotenv = new Dotenv(__DIR__);
$dotenv->load();
$dotenv->required([
  'BASE_URI',
  'CLIENT_ID',
  'CLIENT_SECRET',
  'USER_NAME',
  'USER_PASSWORD',
  'USER_TOKEN',
])->notEmpty();

$client = new Client(
  getenv('BASE_URI'),
  getenv('CLIENT_ID'),
  getenv('CLIENT_SECRET'),
  getenv('USER_NAME'),
  getenv('USER_PASSWORD'),
  getenv('USER_TOKEN')
);

$subscriber = new Subscriber($client, $mail);

$response = new JsonResponse(['mail' => $mail]);
$response->send();
die;