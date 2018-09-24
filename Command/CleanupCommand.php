<?php
/**
 * @file
 * This file is a part of the Os2Display CoreBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Os2Display\CoreBundle\Command;

use Os2Display\CoreBundle\Events\CronEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CleanupCommand
 *
 * @package Os2Display\CoreBundle\Command
 */
class CleanupCommand extends ContainerAwareCommand {
  /**
   * Configure the command
   */
  protected function configure() {
    $this
      ->setName('os2display:core:cleanup')
      ->addOption(
        'dry-run',
        NULL,
        InputOption::VALUE_NONE,
        'Execute the cleanup without applying results to database.'
      )
      ->setDescription('Delete old and unused content.');
  }

  /**
   * Executes the command
   *
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int|null|void
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $io = new SymfonyStyle($input, $output);

    $deleteBeforeDate = $io->confirm('Set threshold for deletion?', false);
    $timestampThreshold = null;

    if ($deleteBeforeDate) {
      $oneYearAgo = strtotime("-1 year", time());
      $date = date("Y-m-d", $oneYearAgo);

      $selectedDate = $io->ask('Which date should be the threshold for deletion?', $date);

      $timestampThreshold = date('U', strtotime($selectedDate));

      $output->writeln('Selected date: ' . $selectedDate . " (" . $timestampThreshold . ")");
    }

    // Get lists of content that should be deleted.
    $cleanupService = $this->getContainer()->get('os2display.core.cleanup_service');

    $mediaList = $cleanupService->findMediaToDelete($timestampThreshold);

    $confirm = $io->confirm('This will delete data. Do you wish to continue?', false);



    $output->writeln('');
    $output->writeln('Cleanp done.');
  }
}
