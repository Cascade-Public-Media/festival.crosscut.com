<?php
require __DIR__ . '/vendor/autoload.php';

use CrosscutFestival\Salesforce\Client;
use CrosscutFestival\Salesforce\Subscriber;
use Dotenv\Dotenv;

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_POST['mail'])) {
  die;
}
$mail = $_POST['mail'];

if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
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

print json_encode(['mail' => $mail]);