<?php

namespace CrosscutFestival\Salesforce;

class Subscriber {

  /**
   * @var Client
   */
  protected $client;

  /**
   * @var string
   */
  protected $mail;

  /**
   * Subscriber constructor.
   *
   * @param Client $client
   *  Salesforce connection client.
   * @param string $mail
   *  Email address to subscribe.
   */
  public function __construct($client, $mail) {
    $this->client = $client;
    $this->mail = $mail;
  }

}
