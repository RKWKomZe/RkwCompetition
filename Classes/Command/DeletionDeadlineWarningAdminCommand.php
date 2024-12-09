<?php
namespace RKW\RkwCompetition\Command;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * DeletionDeadlineWarningAdmin
 *
 *
 * @todo #4206: Nach Ablauf der Löschfrist wird eine automatische E-Mail an alle Admins versendet, dass die Daten nur noch bis zum Ablauf der Löschfrist-Toleranz (default: 14 Tage, via TS) downgeloadet werden können.
 *
 *
 * Execute on CLI with: 'vendor/bin/typo3 rkw_competition:deletionDeadline'
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class DeletionDeadlineWarningAdminCommand extends Command
{


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected ?PersistenceManager $persistenceManager;


    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected ?Logger $logger = null;


    /**
     * @var array
     */
    protected array $settings = [];


    /**
     * @param EventRepository $eventRepository,
     * @param EventReservationRepository $eventReservationRepository,
     * @param PersistenceManager $persistenceManager
     */
    public function __construct(
        EventRepository $eventRepository,
        EventReservationRepository $eventReservationRepository,
        PersistenceManager $persistenceManager
    ) {

        $this->eventRepository = $eventRepository;
        $this->eventReservationRepository = $eventReservationRepository;
        $this->persistenceManager = $persistenceManager;

        parent::__construct();
    }


    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure(): void
    {
        $this->setDescription('Sends an email to all users of an upcoming event.')
            ->addOption(
                'timeFrame',
                't',
                InputOption::VALUE_REQUIRED,
                'Defines when we start to send e-mails as reminder for the user before the event starts in seconds.',
                86400
            );
    }


    /**
     * Executes the command
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @see \Symfony\Component\Console\Input\InputInterface::bind()
     * @see \Symfony\Component\Console\Input\InputInterface::validate()
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $io->newLine();

        $timeFrame = $input->getOption('timeFrame');

        $result = 0;
        try {

            $eventList = $this->eventRepository->findUpcomingEventsForReminder($timeFrame);

            if (count($eventList)) {

                $io->note('Processing ' . count($eventList) . ' events.');

                /** @var \RKW\RkwEvents\Domain\Model\Event $event */
                foreach ($eventList as $event) {
                    if ($eventReservationList = $event->getReservation()) {

                        // send mails
                        /** @var RkwMailService $mailService */
                        $mailService = GeneralUtility::makeInstance(RkwMailService::class);
                        $mailService->informUpcomingEventUser($eventReservationList, $event);

                        $io->note("\t" . 'eventUid: ' . $event->getUid());

                        // set timestamp in event, so that mails are not send twice
                        $event->setReminderMailTstamp(time());
                        $this->eventRepository->update($event);
                        $this->persistenceManager->persistAll();

                        $this->getLogger()->log(LogLevel::INFO, sprintf('Successfully sent %s reminder mails for upcoming event %s.', count($eventReservationList), $event->getUid()));
                    } else {
                        $this->getLogger()->log(LogLevel::INFO, sprintf('No reservations found for upcoming event %s. No reminder mail sent.', $event->getUid()));
                    }
                }
            } else {
                $this->getLogger()->log(LogLevel::INFO, sprintf('No relevant events found for reminder mail.'));
            }

        } catch (\Exception $e) {

            $message = sprintf('An error occurred while trying to send an inform mail about an upcoming event. Message: %s',
                str_replace(["\n", "\r"], '', $e->getMessage())
            );

            // @extensionScannerIgnoreLine
            $io->error($message);
            $this->getLogger()->log(LogLevel::ERROR, $message);
            $result = 1;
        }

        $io->writeln('Done');
        return $result;

    }


    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger(): \TYPO3\CMS\Core\Log\Logger
    {
        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        }

        return $this->logger;
    }

}
