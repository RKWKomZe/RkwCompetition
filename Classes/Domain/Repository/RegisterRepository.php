<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Domain\Repository;


use Madj2k\FeRegister\Domain\Model\FrontendUser;
use RKW\RkwCompetition\Domain\Model\Competition;
use RKW\RkwCompetition\Domain\Model\Register;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
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

        // @toDo: getFirst()?
        return $query->matching(
            $query->logicalAnd(
                $query->equals('frontendUser', $frontendUser),
                $query->equals('competition', $competition)
            )
        )->execute();
    }


    /**
     * function findByCompetitionAndEmail
     *
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @param string $email
     * @return object|RKW\RkwCompetition\Domain\Model\Register
     */
    public function findByCompetitionAndEmail(Competition $competition, string $email): object
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->matching(
            $query->logicalAnd(
                $query->equals('email', $email),
                $query->equals('competition', $competition)
            )
        )->execute()->getFirst();
    }



    /**
     * function findApprovedByCompetition
     *
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findApprovedByCompetition(Competition $competition): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->matching(
            $query->logicalAnd(
                $query->equals('competition', $competition),
                $query->greaterThan('adminApprovedAt', 0)
            )
        )->execute();
    }


    /**
     * function findRefusedByCompetition
     *
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findRefusedByCompetition(Competition $competition): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->matching(
            $query->logicalAnd(
                $query->equals('competition', $competition),
                $query->greaterThan('adminRefusedAt', 0),
                $query->equals('adminApprovedAt', 0)
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
