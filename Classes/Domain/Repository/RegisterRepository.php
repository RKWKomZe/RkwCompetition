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
class RegisterRepository extends AbstractRepository
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
     * function findNotApprovedByCompetition
     *
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findNotApprovedByCompetition(Competition $competition): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->matching(
            $query->logicalAnd(
                $query->equals('competition', $competition),
                $query->equals('adminApprovedAt', 0)
            )
        )->execute();
    }



    /**
     * function findAdminApprovedByCompetition
     *
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAdminApprovedByCompetition(Competition $competition): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->matching(
            $query->logicalAnd(
                $query->equals('competition', $competition),
                $query->logicalNot(
                    $query->equals('adminApprovedAt', 0)
                )
            )
        )->execute();
    }


}
