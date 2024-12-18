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

use RKW\RkwCompetition\Domain\Model\FrontendUser;
use RKW\RkwCompetition\Domain\Model\JuryReference;
use RKW\RkwCompetition\Domain\Repository\CompetitionRepository;
use RKW\RkwCompetition\Domain\Repository\JuryReferenceRepository;
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
 * JuryNotifyCommand
 *
 *
 * @todo #4203: Informiert jury Mitglieder
 *
 *
 * Execute on CLI with: 'vendor/bin/typo3 rkw_competition:juryNotify'
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class JuryNotifyCommand extends Command
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
     * @param CompetitionRepository $competitionRepository
     * @param JuryReferenceRepository $juryReferenceRepository
     * @param PersistenceManager $persistenceManager
     */
    public function __construct(
        CompetitionRepository $competitionRepository,
        JuryReferenceRepository $juryReferenceRepository,
        PersistenceManager $persistenceManager
    ) {
        $this->competitionRepository = $competitionRepository;
        $this->juryReferenceRepository = $juryReferenceRepository;
        $this->persistenceManager = $persistenceManager;

        parent::__construct();
    }


    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure(): void
    {
        $this->setDescription('Sends notify email if competitions register ends. Sends also reminder mails until user has accepted or declined.')
            ->addOption(
                'reminderInterval',
                't',
                InputOption::VALUE_REQUIRED,
                'Set reminder interval in days.',
                3
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

        // @toDos
        // add to jury group (if not already set)
        // mail to jury member


        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $io->newLine();

        $reminderInterval = $input->getOption('reminderInterval');

        $result = 0;
//        try {

            $competitionList = $this->competitionRepository->findBetweenRegisterAndJuryAccessEndForJuryReminder($reminderInterval);

            if (count($competitionList)) {

                $io->note('Processing ' . count($competitionList) . ' competitions.');

                /** @var \RKW\RkwCompetition\Domain\Model\Competition $competition */
                foreach ($competitionList as $competition) {

                    if ($juryCandidateList = $competition->getJuryMemberCandidate()) {

                        /** @var FrontendUser $juryCandidate */
                        foreach ($juryCandidateList as $juryCandidate) {

                            // create juryReference record (IF NOT EXISTS YET)
                            $existingRecord = $this->juryReferenceRepository->findByFrontendUserAndCompetition($juryCandidate, $competition);
                            if ($existingRecord instanceof JuryReference) {
                                /** @var JuryReference $newJuryReference */
                                $newJuryReference = GeneralUtility::makeInstance(JuryReference::class);

                                // the backend (cron) does not know the extension storage PID)
                                $newJuryReference->setPid($competition->getPid());
                                $newJuryReference->setFrontendUser($juryCandidate);
                                $newJuryReference->setCompetition($competition);
                                $newJuryReference->setInvitationMailTstamp(time());
                                $this->juryReferenceRepository->add($newJuryReference);
                            }

                            // add to jury group
                            // avoid double added groups
                            if (!$juryCandidate->getUsergroup()->contains($competition->getGroupForJury())) {
                                $juryCandidate->addUsergroup($competition->getGroupForJury());
                            }
                        }

                        // send mails
                        /** @var RkwMailService $mailService */
                        $mailService = GeneralUtility::makeInstance(RkwMailService::class);
                        $mailService->juryNotifyUser($juryCandidateList->toArray(), $competition);

                        $io->note("\t" . 'competitionUid: ' . $competition->getUid());

                        // set timestamp in competition, so that mails are not send twice
                        $competition->setReminderJuryMailTstamp(time());
                        $this->competitionRepository->update($competition);
                        $this->persistenceManager->persistAll();

                        $this->getLogger()->log(LogLevel::INFO, sprintf('Successfully sent %s reminder mails for jury candidates for competition with UID %s.', count($juryCandidateList), $competition->getUid()));
                    } else {
                        $this->getLogger()->log(LogLevel::INFO, sprintf('No jury candidates found for competition after register end %s. No reminder mail sent.', $competition->getUid()));
                    }
                }
            } else {
                $this->getLogger()->log(LogLevel::INFO, sprintf('No relevant competitions found for jury candidate reminder mail.'));
            }

//        } catch (\Exception $e) {
//
//            $message = sprintf('An error occurred while trying to send an inform mail about an competition within jury period. Message: %s',
//                str_replace(["\n", "\r"], '', $e->getMessage())
//            );
//
//            // @extensionScannerIgnoreLine
//            $io->error($message);
//            $this->getLogger()->log(LogLevel::ERROR, $message);
//            $result = 1;
//        }

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
