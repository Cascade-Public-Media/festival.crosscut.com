<?php

namespace CrosscutFestival\Salesforce;

use Xsolve\SalesforceClient\Request\Query as RequestQuery;
use Xsolve\SalesforceClient\Request\QueryNext;
use Xsolve\SalesforceClient\QueryBuilder\Executor\QueryExecutorInterface;
use Xsolve\SalesforceClient\QueryBuilder\Query;
use Xsolve\SalesforceClient\QueryBuilder\Records;

class QueryExecutor implements QueryExecutorInterface {

  /**
   * @var Client
   */
  private $client;

  /**
   * {@inheritdoc}
   */
  public function __construct(Client $client) {
    $this->client = $client;
  }

  /**
   * {@inheritdoc}
   */
  public function getRecords(Query $query): Records
  {
    $request = new RequestQuery($query->parse());
    $result = $this->client->doRequest($request);

    return new Records($result);
  }

  /**
   * {@inheritdoc}
   */
  public function getNextRecords(Records $records) {
    if (!$records->hasNext()) {
      return;
    }

    $request = new QueryNext($records->getNextIdentifier());
    $nextResult = $this->client->doRequest($request);

    return new Records($nextResult);
  }
}
