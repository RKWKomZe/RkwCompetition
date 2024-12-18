<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Domain\Repository;


use RKW\RkwCompetition\Domain\Model\Competition;
use RKW\RkwCompetition\Domain\Model\FrontendUser;
use RKW\RkwCompetition\Domain\Model\JuryReference;
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
 * The repository for JuryReference
 */
class JuryReferenceRepository extends AbstractRepository
{

    /**
     * Return a reference (getFirst)
     *
     * @param FrontendUser $frontendUser
     * @param Competition $competition
     * @return object
     */
    public function findByFrontendUserAndCompetition(
        FrontendUser $frontendUser,
        Competition $competition
    ): object
    {

        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);

        return $query->matching(
            $query->logicalAnd(
                $query->equals('frontendUser', $frontendUser),
                $query->equals('competition', $competition),
            )

        )->execute()->getFirst();
    }
}
