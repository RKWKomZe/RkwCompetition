<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Domain\Repository;


use Madj2k\FeRegister\Domain\Model\FrontendUser;
use RKW\RkwCompetition\Domain\Model\Competition;
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
 * The repository for Registers
 */
class RegisterRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * function findByCompetitionAndFrontendUser
     *
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */

    public function findByCompetitionAndFrontendUser(Competition $competition, FrontendUser $frontendUser): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->matching(
            $query->logicalAnd(
                $query->equals('frontendUser', $frontendUser),
                $query->equals('competition', $competition)
            )
        )->execute();
    }



    /**
     * function findUnsubmittedByCompetition
     *
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */

    public function findUnsubmittedByCompetition(Competition $competition): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->matching(
            $query->logicalAnd(
                $query->equals('userSubmittedAt', 0),
                $query->equals('competition', $competition)
            )
        )->execute();
    }


}
