<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Domain\Repository;


use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * This file is part of the "RKW Competition" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 Maximilian Fäßler <maximilian@faesslerweb.de>, RKW Kompetenzzentrum
 */

/**
 * The repository for Competitions
 */
class CompetitionRepository extends AbstractRepository
{

    /**
     * Return competitions within register period which are qualified for new reminder mails since the last interval
     *
     * @todo: Really use a daily default interval? Maybe a little bit aggro...
     *
     * @param int $timeInterval
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findWithinRegisterPeriodForReminder(int $timeInterval = 86400): QueryResultInterface
    {

        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->matching(
            $query->logicalAnd(
                $query->logicalAnd(
                    $query->lessThanOrEqual('registerStart', time()),
                    $query->greaterThan('registerEnd', time())
                ),
                $query->lessThanOrEqual('reminderIncompleteMailTstamp', time() - $timeInterval)
            )

        )->execute();
    }


    /**
     * Return competitions within removal period (if a date is set)
     *
     * @param int $timeFrameDays
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findWithinRemovalRangeIfSetForReminder(int $timeFrameDays = 14): QueryResultInterface
    {

        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->matching(
            $query->logicalAnd(
                $query->lessThanOrEqual(
                    'recordRemovalDate',
                    strtotime('+' . $timeFrameDays . ' days', time())
                ),
                // ! exclude all where no removal date is set !
                $query->logicalNot(
                    $query->equals('recordRemovalDate', 0)
                ),
                $query->equals('reminderCleanupMailTstamp', 0)
            )

        )->execute();
    }



    /**
     * Return competitions between register_end and jury_access_end
     *
     * @param int $timeFrameDays
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findBetweenRegisterAndJuryAccessEndForJuryReminder(int $timeFrameDays = 3): QueryResultInterface
    {

        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->matching(
            $query->logicalAnd(
                $query->lessThanOrEqual('registerEnd', time()),
                $query->greaterThanOrEqual('juryAccessEnd', time()),
                $query->lessThanOrEqual(
                    'reminderJuryMailTstamp',
                    strtotime('+' . $timeFrameDays . ' days', time())
                ),
            )

        )->execute();
    }


    /**
     * Return competitions after registerEnd without closingDayMailTimestamp
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findAfterRegisterPeriodForReminder(): QueryResultInterface
    {

        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->matching(
            $query->logicalAnd(
                $query->lessThanOrEqual('registerEnd', time()),
                $query->equals('closingDayMailTstamp', 0),
            )

        )->execute();
    }


    /**
     * Return competitions with expired removal date
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function findExpiredWithRemovalDate(): QueryResultInterface
    {
        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->matching(
            $query->logicalAnd(
                $query->lessThanOrEqual('recordRemovalDate', time()),
                $query->logicalNot(
                    $query->equals('recordRemovalDate', 0)
                )
            )

        )->execute();
    }
}
