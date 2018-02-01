<?php

namespace Drupal\ib3_reports\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Drupal\Console\Core\Command\Command;
use Drupal\Console\Core\Style\DrupalStyle;
use Drupal\Console\Annotations\DrupalCommand;

/**
 * Class PagesCommand.
 *
 * @DrupalCommand (
 *     extension="ib3_reports",
 *     extensionType="module"
 * )
 */
class PagesCommand extends Command {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('ib3_reports:pages')
      ->setDescription($this->trans('commands.ib3_reports.pages.description'));
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $io = new DrupalStyle($input, $output);
    $this->process();
    $io->info($this->trans('commands.ib3_reports.pages.messages.success'));
  }

  public function process() {
    $process = new Process('./vendor/bin/phpunit --testsuite pages --log-json=./tests/results/pages.json', \Drupal::root());
    $process->run();
  }

}
