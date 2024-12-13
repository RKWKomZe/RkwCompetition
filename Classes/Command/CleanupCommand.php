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

use RKW\RkwCompetition\Domain\Model\Competition;
use RKW\RkwCompetition\Domain\Model\Register;
use RKW\RkwCompetition\Domain\Model\Upload;
use RKW\RkwCompetition\Domain\Repository\CompetitionRepository;
use RKW\RkwCompetition\Domain\Repository\JuryReferenceRepository;
use RKW\RkwCompetition\Domain\Repository\RegisterRepository;
use RKW\RkwCompetition\Domain\Repository\UploadRepository;
use RKW\RkwCompetition\Persistence\Cleaner;
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
 * class CleanupCommand
 *
 * Execute on CLI with: 'vendor/bin/typo3 rkw_competition:cleanup'
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CleanupCommand extends Command
{

    /**
     * @var Cleaner|null
     */
    protected ?Cleaner $cleaner = null;


    /**
     * @var \TYPO3\CMS\Core\Log\Logger|null
     */
    protected ?Logger $logger = null;

    /**
     * @param CompetitionRepository $competitionRepository
     * @param RegisterRepository $registerRepository
     * @param UploadRepository $uploadRepository
     * @param JuryReferenceRepository $juryReferenceRepository
     * @param PersistenceManager $persistenceManager
     */
    public function __construct(
        CompetitionRepository $competitionRepository,
        RegisterRepository $registerRepository,
        UploadRepository $uploadRepository,
        JuryReferenceRepository $juryReferenceRepository,
        PersistenceManager $persistenceManager
    ) {

        $this->competitionRepository = $competitionRepository;
        $this->registerRepository = $registerRepository;
        $this->uploadRepository = $uploadRepository;
        $this->juryReferenceRepository = $juryReferenceRepository;
        $this->persistenceManager = $persistenceManager;

        parent::__construct();
    }

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure(): void
    {
        $this->setDescription('Removes expired competitions if a removal date is set.')
            ->addOption(
                'dryRun',
                't',
                InputOption::VALUE_REQUIRED,
                'Do a dry-run without making changes (default: 1).',
                1
            );
    }


    /**
     * Executes the command for showing sys_log entries
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

        $dryRun = $input->getOption('dryRun');

        $io->note('Using dryRun="' . $dryRun .'"');

        $result = 0;
//        try {

            $competitionList = $this->competitionRepository->findExpiredWithRemovalDate();

            $message = 'Removed ' . count($competitionList) . ' competition(s).';

            /** @var Competition $competition */
            foreach ($competitionList as $competition) {

                // ###############################################################
                // ### REMOVE REGISTER (+ related UPLOAD records) ###
                $message = 'Removed ' . $competition->getRegister()->count() . ' register(s) with related upload records.';
                /** @var Register $register */
                foreach ($competition->getRegister() as $register) {
                    if (! $dryRun) {
                        if ($register->getUpload() instanceof Upload) {
                            // REMOVE RELATED UPLOAD RECORD
                            $this->uploadRepository->removeHard($register->getUpload());
                        }
                        // REMOVE REGISTER
                        $this->registerRepository->removeHard($register);
                    }
                }
                $io->note($message);
                $this->getLogger()->log(LogLevel::INFO, $message);


                // ###############################################################
                // ### REMOVE JURY REFERENCE RECORDS ###
//                $juryReferenceList = $this->juryReferenceRepository->findByCompetition($competition);
//                $message = 'Removed ' . count($juryReferenceList) . ' jury reference(s).';
//                foreach ($juryReferenceList as $juryReference) {
//                    if (! $dryRun) {
//                        $this->juryReferenceRepository->removeHard($juryReference);
//                    }
//                }
//                $io->note($message);
//                $this->getLogger()->log(LogLevel::INFO, $message);


                // ###############################################################
                // ### REMOVE COMPETITION ###
                if (! $dryRun) {
                    $this->competitionRepository->removeHard($competition);
                }
            }
            $io->note($message);
            $this->getLogger()->log(LogLevel::INFO, $message);

//        } catch (\Exception $e) {
//
//            $message = sprintf('An unexpected error occurred while trying to cleanup: %s',
//                str_replace(array("\n", "\r"), '', $e->getMessage())
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
    protected function getLogger(): Logger
    {
        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        }

        return $this->logger;
    }
}
