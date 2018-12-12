<?php
require __DIR__ . '/vendor/autoload.php';

use CrosscutFestival\Response;
use CrosscutFestival\Salesforce\Client;
use CrosscutFestival\Salesforce\Subscriber;
use Noodlehaus\Config;
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();
$response = new Response();

$config = new Config('config.json');
$config_required = [
  'client.base_uri', 'client.id', 'client.secret',
  'contact.search',
  'list_field',
  'timezone',
  'user.name', 'user.password', 'user.token',
];
foreach ($config_required as $key) {
  if (empty($config[$key])) {
    $response->setError('Required API configuration missing.')->send();
  }
}

$mail = $request->get('mail');
if (empty($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
  $response->setError('Invalid or empty email address.')->send();
}

$client = new Client(
  $config->get('client.base_uri'),
  $config->get('client.id'),
  $config->get('client.secret'),
  $config->get('user.name'),
  $config->get('user.password'),
  $config->get('user.token')
);

// TODO: Handle exceptions/errors from Salesforce API.

$subscriber = new Subscriber($client, $mail, $config);
$subscriber->subscribe();

$response->setData(['mail' => $mail])->send();
