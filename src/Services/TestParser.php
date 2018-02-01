<?php

namespace Drupal\ib3_reports\Services;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TestParser {

  private $tests;
  private $results;

  protected $loggerFactory;

  public function __construct(LoggerChannelFactoryInterface $iLoggerFactory)
  {
    $this->loggerFactory = $iLoggerFactory;
    return $this;
  }

  public static function create(ContainerInterface $container)
  {
    $loggerFactory = $container->get('logger.factory');
    return new static($loggerFactory);
  }

  public function load($url)
  {
    touch($url);
    $not_quite_json = file_get_contents($url);
    $json = '[' . str_replace('}{', '},{', $not_quite_json) . ']';
    $this->tests = Json::decode($json);

    $this->loggerFactory->get('ib3 test parser')
      ->debug('Loaded raw test report.');

    return $this;
  }

  public function prepareResults()
  {
    foreach ($this->getTests() as $test) {
      if ($this->notTest($test)) continue;

      $result = $this->prepareResult($test);
      $this->addResult($result);
    }

    $this->loggerFactory->get('ib3 test parser')
      ->debug('Prepared results.');
  }

  public function getResults()
  {
    return $this->results;
  }

  private function getTests()
  {
    return $this->tests;
  }

  private function notTest($test)
  {
    return ($test['event'] != 'test') ? true : false;
  }

  private function addResult($result)
  {
    $this->results[] = $result;
  }

  private function prepareSuite($raw_suite)
  {
    $namespace = explode('::', $raw_suite);
    $suite = explode('\\', $namespace[0]);
    return str_replace('_', ' ', end($suite));
  }

  private function prepareName($raw_name)
  {
    $name = explode('::',$raw_name)[1];
    return str_replace('_', ' ', $name);
  }

  private function prepareResult($test)
  {
    $arr = [
      'suite' => $this->prepareSuite($test['suite']),
      'name' => $this->prepareName($test['test']),
      'status' => $test['status'],
    ];

    $this->loggerFactory->get('ib3 test parser')
      ->debug('Prepared result.');

    return $arr;
  }
}
