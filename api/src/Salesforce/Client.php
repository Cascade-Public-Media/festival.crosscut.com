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

  public function __construct($base_uri, $client_id, $client_secret, $user_name, $user_password, $user_token) {
    $client = new GuzzleAdapterClient(new GuzzleClient([
      'base_uri' => $base_uri,
    ]));

    $credentials = new Credentials(
      $client_id,
      $client_secret,
      'password',
      ['username' => $user_name, 'password' => $user_password.$user_token]
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
