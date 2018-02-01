<?php

namespace Drupal\ib3_reports\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ib3_reports\Services\TestParser;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ib3_reports\Command\PagesCommand;
use Drupal\ib3_reports\Command\ModulesCommand;

class ReportsController extends ControllerBase {

  protected $testParser;
  protected $loggerFactory;

  public function __construct(TestParser $testParser, LoggerChannelFactoryInterface $iLoggerFactory)
  {
    $this->testParser = $testParser;
    $this->loggerFactory = $iLoggerFactory;
  }

  public static function create(ContainerInterface $container)
  {
    $testParser = $container->get('ib3_reports.test_parser');
    $loggerFactory = $container->get('logger.factory');
    return new static($testParser, $loggerFactory);
  }

  /**
   * Display the markup.
   *
   * @return array
   */
  public function index() {
    return [
     '#theme' => 'index',
     '#heading' => $this->t('Health'),
    ];
  }

  /**
   * Display the markup.
   *
   * @return array
   */
  public function pages() {

    if (array_key_exists('ib3-reports-refresh', \Drupal::request()->request->all())) {

      $this->loggerFactory->get('ib3 test pages')
        ->debug('Create the page tests.');

      $command = new PagesCommand();
      $command->process();
    }

    $this->loggerFactory->get('ib3 test pages')
      ->debug('Requesting results.');

    $tests = $this->testParser
      ->load(\Drupal::root() . '/tests/results/pages.json')
      ->prepareResults();

    $this->loggerFactory->get('ib3 test pages')
      ->debug('Received results.');

    return [
     '#theme' => 'report',
     '#heading' => $this->t('Page Tests'),
     '#results' => $this->testParser->getResults(),
     '#page' => null,
    ];
  }

  /**
   * Display the markup.
   *
   * @return array
   */
  public function modules() {

    if (array_key_exists('ib3-reports-refresh', \Drupal::request()->request->all())) {

      $this->loggerFactory->get('ib3 test modules')
        ->debug('Create the module tests.');

      $command = new ModulesCommand();
      $command->process();
    }

    $this->loggerFactory->get('ib3 test modules')
      ->debug('Requesting results.');

    $tests = $this->testParser
      ->load(\Drupal::root() . '/tests/results/modules.json')
      ->prepareResults();

    $this->loggerFactory->get('ib3 test modules')
      ->debug('Received results.');

    return [
     '#theme' => 'report',
     '#heading' => $this->t('Module Tests'),
     '#results' => $this->testParser->getResults(),
     '#page' => null,
    ];
  }

}
