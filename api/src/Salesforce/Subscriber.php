<?php

namespace CrosscutFestival\Salesforce;

use Xsolve\SalesforceClient\Enum\SObjectType;
use Xsolve\SalesforceClient\QueryBuilder\Expr\ExpressionFactory;
use Xsolve\SalesforceClient\QueryBuilder\QueryBuilder;
use Xsolve\SalesforceClient\Request\Create;
use Xsolve\SalesforceClient\Request\Update;

class Subscriber {

  /**
   * Salesforce client.
   *
   * @var Client
   */
  protected $client;

  /**
   * Email address to subscribe
   *
   * @var string
   */
  protected $mail;

  /**
   * API configuration.
   *
   * @var \Noodlehaus\Config
   */
  protected $config;

  /**
   * Subscriber constructor.
   *
   * @param Client $client
   *  Salesforce connection client.
   * @param string $mail
   *  Email address to subscribe.
   * @param \Noodlehaus\Config $config
   *  Config object.
   */
  public function __construct($client, $mail, $config) {
    $this->client = $client;
    $this->mail = $mail;
    $this->config = $config;
  }

  /**
   * Subscribe an email address to the configured mailing list.
   *
   * This method will also create, if necessary, a Salesforce Account and
   * associated Contact object for the email address.
   */
  public function subscribe() {
    if ($contact_id = self::getOrCreateContact()) {
      $this->client->doRequest(new Update(
        SObjectType::CONTACT(),
        $contact_id,
        [$this->config->get('list_field') => TRUE]
      ));
    }
  }

  /**
   * Get or create a Salesforce Contact to update.
   */
  private function getOrCreateContact() {
    $contact_id = NULL;

    $e = new ExpressionFactory();
    $query = new QueryBuilder();
    $queryExecutor = new QueryExecutor($this->client);

    $list_field = $this->config->get('list_field');
    $query->select($e->fields(['Id', $list_field]))
      ->from($e->objectType(SObjectType::CONTACT()));
    foreach ($this->config->get('contact.search') as $field) {
      $query->orWhere($e->equals($field, '{mail}'));
    }
    $query->setParameters(['mail' => $this->mail]);
    $query->limit(1);

    // TODO: Error handling in $queryExecutor.
    $contact = $queryExecutor->getRecords($query->getQuery())
      ->getIterator()
      ->current();

    if (!is_null($contact) && !$contact[$list_field]) {
      $contact_id = $contact['Id'];
    }
    elseif (is_null($contact)) {
      if ($account_id = self::createSalesforceAccount()) {
        $contact_id = self::createSalesforceContact($account_id);
      }
    }

    return $contact_id;
  }

  /**
   * Create a Salesforce Account object.
   *
   * @return string|null
   *   ID for the newly created Account object or NULL on failure.
   */
  private function createSalesforceAccount() {
    $account_id = NULL;
    $params = self::processDefaults($this->config->get('account.defaults', []));
    $account_request = new Create(SObjectType::ACCOUNT(), $params);
    $result = $this->client->doRequest($account_request);
    if (isset($result['id'])) {
      $account_id = $result['id'];
    }
    return $account_id;
  }

  /**
   * Create a Salesforce Contact with only an email address.
   *
   * @param string $account_id
   *   Parent Salesforce Account object ID.
   *
   * @return string|null
   *   ID for the newly created Contact object or NULL on failure.
   */
  private function createSalesforceContact($account_id) {
    $contact_id = NULL;
    $params = self::processDefaults($this->config->get('contact.defaults', []));
    $params['AccountId'] = $account_id;
    $contact_request = new Create(SObjectType::CONTACT(), $params);
    $result = $this->client->doRequest($contact_request);
    if (isset($result['id'])) {
      $contact_id = $result['id'];
    }
    return $contact_id;
  }

  /**
   * Process special default values from the API config.
   *
   * @param array $defaults
   *   Key-value array of defaults with Salesforce field names for keys.
   *
   * @return array
   *   Finalized version of the default values.
   */
  private function processDefaults(array $defaults) {
    foreach ($defaults as $field => $value) {
      // Cast to string to prevent issues with boolean values.
      switch ((string) $value) {
        case '{email}':
          $defaults[$field] = $this->mail;
          break;
        case '{now}':
          try {
            $date = new \DateTime(
              'now',
              new \DateTimeZone($this->config->get('timezone'))
            );
            $defaults[$field] = $date->format('Y-m-d');
          }
          catch (\Exception $e) {}
          break;
      }
    }
    return $defaults;
  }

}
