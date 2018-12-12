<?php

namespace CrosscutFestival\Salesforce;

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapterClient;
use Xsolve\SalesforceClient\Client\SalesforceClient;
use Xsolve\SalesforceClient\Generator\TokenGenerator;
use Xsolve\SalesforceClient\Security\Authentication\Authenticator;
use Xsolve\SalesforceClient\Security\Authentication\Credentials;
use Xsolve\SalesforceClient\Security\Authentication\Strategy\PasswordGrantRegenerateStrategy;
use Xsolve\SalesforceClient\Storage\RequestTokenStorage;

class Client extends SalesforceClient {

  /**
   * @var string
   */
  protected $base_uri;

  /**
   * @var string
   */
  protected $client_id;

  /**
   * @var string
   */
  protected $client_secret;

  /**
   * @var string
   */
  protected $user_name;

  /**
   * @var string
   */
  protected $user_password;

  /**
   * @var string
   */
  protected $user_token;

  /**
   * Client constructor.
   *
   * @param \Noodlehaus\Config $config
   *   API config manager.
   */
  public function __construct($config) {
    $client = new GuzzleAdapterClient(new GuzzleClient([
      'base_uri' => $config->get('client.base_uri'),
      'http_errors' => FALSE,
    ]));

    $credentials = new Credentials(
      $config->get('client.id'),
      $config->get('client.secret'),
      'password',
      [
        'username' => $config->get('user.name'),
        'password' => $config->get('user.password').$config->get('user.token')
      ]
    );

    $authenticator = new Authenticator(
      $client,
      [new PasswordGrantRegenerateStrategy()]
    );

    $tokenGenerator = new TokenGenerator(
      $credentials,
      $authenticator,
      // TODO: Override and create a more performant storage method.
      new RequestTokenStorage()
    );

    parent::__construct($client, $tokenGenerator, 'v44.0');
  }

}
