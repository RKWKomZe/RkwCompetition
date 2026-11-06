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

use Madj2k\CoreExtended\Utility\FrontendSimulatorUtility;
use RKW\RkwCompetition\Api\OwnCloud;
use RKW\RkwCompetition\Domain\Repository\CompetitionRepository;
use RKW\RkwCompetition\Domain\Repository\RegisterRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * UpdateOwnCloudExpireDateCommand
 *
 * Updates the OwnCloud share expire date for all registrations of a specific competition.
 *
 * Execute on CLI with: 'vendor/bin/typo3 rkw_competition:updateOwnCloudExpireDate <COMPETITION_UID>'
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class UpdateOwnCloudExpireDateCommand extends Command
{

    /**
     * @var CompetitionRepository
     */
    protected CompetitionRepository $competitionRepository;

    /**
     * @var RegisterRepository
     */
    protected RegisterRepository $registerRepository;

    /**
     * @var \TYPO3\CMS\Core\Log\Logger|null
     */
    protected ?Logger $logger = null;

    /**
     * @param CompetitionRepository $competitionRepository
     * @param RegisterRepository $registerRepository
     */
    public function __construct(
        CompetitionRepository $competitionRepository,
        RegisterRepository $registerRepository
    ) {
        $this->competitionRepository = $competitionRepository;
        $this->registerRepository = $registerRepository;

        parent::__construct();
    }

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure(): void
    {
        $this->setDescription('Updates the OwnCloud share expire date for all registrations of a specific competition.')
            ->addArgument(
                'competitionUid',
                InputArgument::REQUIRED,
                'The UID of the competition'
            )
            ->addOption(
                'rootPageUid',
                'r',
                InputOption::VALUE_OPTIONAL,
                'The root page UID to use for loading TypoScript settings.',
                1
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

        $competitionUid = (int)$input->getArgument('competitionUid');
        $rootPageUid = (int)$input->getOption('rootPageUid');

        try {
            FrontendSimulatorUtility::simulateFrontendEnvironment($rootPageUid);

            $competition = $this->competitionRepository->findByIdentifier($competitionUid);
            if (!$competition) {
                $message = 'Competition with UID ' . $competitionUid . ' not found.';
                $io->error($message);
                $this->getLogger()->log(LogLevel::ERROR, $message);
                return Command::FAILURE;
            }

            $newExpireDate = $competition->getRegisterEnd();
            if (!$newExpireDate) {
                $message = 'No registerEnd date found for competition ' . $competitionUid . '.';
                $io->error($message);
                $this->getLogger()->log(LogLevel::ERROR, $message);
                return Command::FAILURE;
            }

            $registrations = $this->registerRepository->findByCompetition($competition);
            $ownCloud = GeneralUtility::makeInstance(OwnCloud::class);
            $settings = \Madj2k\CoreExtended\Utility\GeneralUtility::getTypoScriptConfiguration('Rkwcompetition', \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
            $basePath = $settings['api']['ownCloud']['folderStructure']['basePath'] ?? 'rkw_competition';
            $folderCreatePath = GeneralUtility::trimExplode('/', $basePath, true);
            $competitionFolderName = 'competition_uid_' . $competition->getUid();

            $io->note('Updating shares for competition ' . $competition->getTitle());

            $shareIdsToUpdate = [];

            // 1. Global list of shares (with subfiles=false) filtered by competition folder name
            $allShares = $ownCloud->getShareApi()->getAllShares(
                [],
                'json',
                true,
                '',
                '',
                false
            );

            if (!empty($allShares) && is_array($allShares)) {
                $io->text('Found ' . count($allShares) . ' total share(s) on OwnCloud via global query.');
                foreach ($allShares as $shareData) {
                    $shareId = (int)($shareData['element']['id'] ?? $shareData['id'] ?? 0);
                    $fileTarget = $shareData['path'] ?? $shareData['file_target'] ?? $shareData['url'] ?? '';
                    $expireDate = $shareData['expiration'] ?? $shareData['expireDate'] ?? 'N/A';

                    if ($shareId > 0 && strpos($fileTarget, $competitionFolderName) !== false) {
                        $description = (strpos($fileTarget, $competitionFolderName . '/feuser_uid_') !== false) ? 'user folder' : 'competition folder';
                        if (!isset($shareIdsToUpdate[$shareId])) {
                            $shareIdsToUpdate[$shareId] = $description . ' (' . ($fileTarget ?: 'global match') . ')';
                            $io->text(' -> Found matching share ID ' . $shareId . ' (Target: ' . ($fileTarget ?: 'N/A') . ', ExpireDate: ' . $expireDate . ')');
                        }
                    }
                }
            } else {
                $io->text('No shares returned by global OwnCloud API query.');
            }

            // 2. Specific query for the competition folder itself
            $competitionShares = $ownCloud->getShareApi()->getAllShares(
                array_merge($folderCreatePath, [$competitionFolderName]),
                'json',
                true,
                '',
                '',
                false
            );

            if (!empty($competitionShares) && is_array($competitionShares)) {
                foreach ($competitionShares as $shareData) {
                    $shareId = (int)($shareData['element']['id'] ?? $shareData['id'] ?? 0);
                    $fileTarget = $shareData['path'] ?? $shareData['file_target'] ?? $shareData['url'] ?? '';
                    $expireDate = $shareData['expiration'] ?? $shareData['expireDate'] ?? 'N/A';
                    if ($shareId > 0 && !isset($shareIdsToUpdate[$shareId])) {
                        $shareIdsToUpdate[$shareId] = 'competition folder (' . $competitionFolderName . ')';
                        $io->text(' -> Found competition folder share ID ' . $shareId . ' (Target: ' . ($fileTarget ?: 'N/A') . ', ExpireDate: ' . $expireDate . ')');
                    }
                }
            }

            // 3. Specific query for each user registration folder
            foreach ($registrations as $register) {
                if (!$register->getFrontendUser()) {
                    continue;
                }

                $userFolderName = 'feuser_uid_' . $register->getFrontendUser()->getUid() . '_' . $register->getFrontendUser()->getEmail();

                $userShares = $ownCloud->getShareApi()->getAllShares(
                    array_merge($folderCreatePath, [$competitionFolderName], [$userFolderName]),
                    'json',
                    true,
                    '',
                    '',
                    false
                );

                if (!empty($userShares) && is_array($userShares)) {
                    foreach ($userShares as $shareData) {
                        $shareId = (int)($shareData['element']['id'] ?? $shareData['id'] ?? 0);
                        $fileTarget = $shareData['path'] ?? $shareData['file_target'] ?? $shareData['url'] ?? '';
                        $expireDate = $shareData['expiration'] ?? $shareData['expireDate'] ?? 'N/A';
                        if ($shareId > 0 && !isset($shareIdsToUpdate[$shareId])) {
                            $shareIdsToUpdate[$shareId] = 'user folder (' . $register->getFrontendUser()->getEmail() . ')';
                            $io->text(' -> Found user folder share ID ' . $shareId . ' (Target: ' . ($fileTarget ?: 'N/A') . ', ExpireDate: ' . $expireDate . ')');
                        }
                    }
                }
            }

            if (empty($shareIdsToUpdate)) {
                $io->note('No shares found for competition ' . $competition->getTitle());
            }

            // 4. Update all collected unique share IDs
            $formattedExpireDate = (clone $newExpireDate)->modify('+1 day')->format('Y-m-d');
            foreach ($shareIdsToUpdate as $shareId => $description) {
                $ownCloud->getShareApi()->updateShare(
                    $shareId,
                    null,
                    '',
                    false,
                    $formattedExpireDate
                );
                $io->text('Updated share ID ' . $shareId . ' for ' . $description);
                $this->getLogger()->log(LogLevel::INFO, 'Updated share ID ' . $shareId . ' for ' . $description . ' in competition ' . $competitionUid);
            }

            $io->success('OwnCloud share expire dates successfully updated.');
            $this->getLogger()->log(LogLevel::INFO, 'OwnCloud share expire dates successfully updated for competition ' . $competitionUid);
            return Command::SUCCESS;

        } finally {
            FrontendSimulatorUtility::resetFrontendEnvironment();
        }
    }

    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger(): \TYPO3\CMS\Core\Log\Logger
    {
        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        }

        return $this->logger;
    }
}
