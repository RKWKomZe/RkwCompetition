<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Domain\Repository;


use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

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
class CompetitionRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
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
                $query->lessThanOrEqual('reminderMailTstamp', time() - $timeInterval)
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
                $query->lessThanOrEqual('recordRemovalDate', time() + $timeFrameDays . ' days'),
                // ! exclude all where no removal date is set !
                $query->logicalNot(
                    $query->equals('recordRemovalDate', 0)
                )
            )

        )->execute();
    }
}
