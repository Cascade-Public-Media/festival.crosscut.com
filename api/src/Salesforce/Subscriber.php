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

  public function __construct($client, $mail) {
    $this->client = $client;
    $this->mail = $mail;
  }

}
