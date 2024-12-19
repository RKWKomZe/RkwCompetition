<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Controller;


/**
 * This file is part of the "RKW Competition" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 Maximilian Fäßler <maximilian@faesslerweb.de>, RKW Kompetenzzentrum
 */

/**
 * CompetitionController
 */
class CompetitionController extends \RKW\RkwCompetition\Controller\AbstractController
{

    /**
     * competitionRepository
     *
     * @var \RKW\RkwCompetition\Domain\Repository\CompetitionRepository
     */
    protected $competitionRepository = null;

    /**
     * @param \RKW\RkwCompetition\Domain\Repository\CompetitionRepository $competitionRepository
     */
    public function injectCompetitionRepository(\RKW\RkwCompetition\Domain\Repository\CompetitionRepository $competitionRepository)
    {
        $this->competitionRepository = $competitionRepository;
    }

    /**
     * action show
     *
     * @param \RKW\RkwCompetition\Domain\Model\Competition|null $competition
     * @return void
     */
    public function showAction(\RKW\RkwCompetition\Domain\Model\Competition $competition = null)
    {

        $this->view->assign(
            'competition', $competition ?: $this->competitionRepository->findByIdentifier(intval($this->settings['selectedCompetition']))
        );
    }
}
