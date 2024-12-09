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

use RKW\RkwCompetition\Domain\Repository\CompetitionRepository;
use RKW\RkwCompetition\Domain\Repository\RegisterRepository;
use RKW\RkwCompetition\Service\RkwMailService;
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
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * IncompleteUserRegistrationCommand
 *
 *
 * @todo #4200: Reminder für Upload der Daten in definierbaren Abständen (via Wettbewerbsdatensatz) bis zum Abgabeschluss, wenn Daten noch nicht eingereicht wurden.
 *
 * Execute on CLI with: 'vendor/bin/typo3 rkw_competition:incompleteUserRegistration'
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class IncompleteUserRegistrationCommand extends Command
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
     * @param CompetitionRepository $competitionRepository,
     * @param RegisterRepository $registerRepository,
     * @param PersistenceManager $persistenceManager
     */
    public function __construct(
        CompetitionRepository $competitionRepository,
        RegisterRepository $registerRepository,
        PersistenceManager $persistenceManager
    ) {

        $this->competitionRepository = $competitionRepository;
        $this->registerRepository = $registerRepository;
        $this->persistenceManager = $persistenceManager;

        parent::__construct();
    }


    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure(): void
    {
        $this->setDescription('Sends an email to all users with an incomplete registration.')
            ->addOption(
                'timeInterval',
                't',
                InputOption::VALUE_REQUIRED,
                'Defines the length of one interval to send e-mails as reminder for the user to complete and submit their registration.',
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

        $timeInterval = $input->getOption('timeInterval');

        $result = 0;
        try {

            $competitionList = $this->competitionRepository->findWithinRegisterPeriodForReminder($timeInterval);

            if (count($competitionList)) {

                $io->note('Processing ' . count($competitionList) . ' competitions.');

                /** @var \RKW\RkwCompetition\Domain\Model\Competition $competition */
                foreach ($competitionList as $competition) {

                    if ($registerList = $this->registerRepository->findUnsubmittedByCompetition($competition)) {

                        // send mails
                        /** @var RkwMailService $mailService */
                        $mailService = GeneralUtility::makeInstance(RkwMailService::class);
                        $mailService->incompleteRegisterUser($registerList);

                        $io->note("\t" . 'competitionUid: ' . $competition->getUid());

                        // set timestamp in event, so that mails are not send twice
                        $competition->setReminderMailTstamp(time());
                        $this->competitionRepository->update($competition);
                        $this->persistenceManager->persistAll();

                        $this->getLogger()->log(LogLevel::INFO, sprintf('Successfully sent %s reminder mails for competition within register period %s.', count($registerList), $competition->getUid()));
                    } else {
                        $this->getLogger()->log(LogLevel::INFO, sprintf('No reservations found for competition in register period %s. No reminder mail sent.', $competition->getUid()));
                    }
                }
            } else {
                $this->getLogger()->log(LogLevel::INFO, sprintf('No relevant competitions found for reminder mail.'));
            }

        } catch (\Exception $e) {

            $message = sprintf('An error occurred while trying to send an inform mail about an competition within register period. Message: %s',
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
