<?php
namespace RKW\RkwCompetition\Persistence;

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

use Madj2k\CoreExtended\Utility\GeneralUtility;
use Madj2k\FeRegister\Domain\Repository\CleanerInterface;
use Madj2k\FeRegister\Domain\Repository\FrontendUserRepository;
use Madj2k\FeRegister\Domain\Repository\GuestUserRepository;
use Madj2k\FeRegister\Domain\Repository\OptInRepository;
use Madj2k\FeRegister\Domain\Repository\ConsentRepository;
use RKW\RkwCompetition\Domain\Model\Competition;
use SJBR\StaticInfoTables\Domain\Model\AbstractEntity;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class Cleaner
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Cleaner
{

    /**
     * @var bool
     */
    protected bool $dryRun = false;


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?PersistenceManager $persistenceManager = null;


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    public function injectPersistenceManager(PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }


    /**
     * @var Logger|null
     */
    protected ?Logger $logger = null;


    /**
     * Sets dryRun parameter
     *
     * @param bool $dryRun
     * @return void
     */
    public function setDryRun (bool $dryRun): void
    {
        $this->dryRun = $dryRun;
    }


    /**
     *
     * @param array $competitionList
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function removeCompetitionList (array $competitionList): int
    {
        foreach ($competitionList as $competition) {
            $competitionRepository->removeHard($object);
        }

    }


    /**
     *
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function removeRegisterByCompetition (): int
    {
        return $this->remove($this->guestUserRepository, $daysSinceExpired);
    }


    /**
     *
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function removeJuryRelationsByCompetition (): int
    {
        return $this->remove($this->guestUserRepository, $daysSinceExpired);
    }


    /**
     *
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function removeUploadRecordByFrontendUser (): int
    {
        // @toDo: Remove upload records from DB

        return $this->remove($this->frontendUserRepository, $daysSinceExpired);
    }



    /**
     *
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function removeUploadFolderFromHddByFrontendUser (): int
    {
        // @toDo: Remove upload folder

        return $this->remove($this->frontendUserRepository, $daysSinceExpired);
    }


    /**
     *
     * @param int $daysSinceExpired
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function removeCloudFilesByRegister (): int
    {
        return $this->remove($this->frontendUserRepository, $daysSinceExpired);
    }



    /**
     * Removes expired objects really from database
     *
     * @param int $daysSinceExpired
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    public function removeConsents (int $daysSinceExpired = 30): int
    {
        return $this->remove($this->consentRepository, $daysSinceExpired);
    }


    /**
     * Removes expired objects really from database
     *
     * @param \Madj2k\FeRegister\Domain\Repository\CleanerInterface $repository
     * @param AbstractEntity $entity
     * @return int
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception
     */
    protected function remove(CleanerInterface $repository, array $recordsToRemove): int
    {

        /** @var \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $object */
        foreach ($expiredOptIns as $object) {

            if (! $this->dryRun) {
                $repository->removeHard($object);
            }
            $cnt++;

            $this->getLogger()->log(
                LogLevel::INFO,
                sprintf(
                    (($this->dryRun) ? 'Dry-Run: ' : '' ). 'Deleted id %s of object "%s".',
                    $object->getUid(),
                    get_class($object),

                )
            );
        }

        return $cnt;
    }


    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger(): Logger
    {

        if (!$this->logger instanceof Logger) {
            $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        }

        return $this->logger;
    }

}


