<?php
namespace RKW\RkwCompetition\Controller;

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

use RKW\RkwCompetition\Domain\Repository\CompetitionRepository;
use RKW\RkwCompetition\Domain\Repository\RegisterRepository;
use RKW\RkwCheckup\Domain\Model\Checkup;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class BackendController
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendController extends ActionController
{
    /**
     * registerRepository
     *
     * @var \RKW\RkwCompetition\Domain\Repository\RegisterRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected RegisterRepository $registerRepository;

    /**
     * competitionRepository
     *
     * @var \RKW\RkwCompetition\Domain\Repository\CompetitionRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected CompetitionRepository $competitionRepository;


    /**
     * action list
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register|null $register
     * @return void
     */
    public function listAction(Checkup $checkup = null): void
    {
        if ($checkup) {
            $this->view->assign('checkResultList', $this->resultRepository->getFinishedByCheck($checkup));
            $this->view->assign('checkup', $checkup);
        } else {
            $this->view->assign('competitionList', $this->competitionRepository->findAllIgnorePid());
        }
    }


    /**
     * action show
     *
     * @param \RKW\RkwCheckup\Domain\Model\Checkup
     * @return void
     */
    public function showAction(Checkup $checkup): void
    {
        $this->view->assign('checkupResultCountTotal', $this->resultRepository->getFinishedByCheck($checkup)->count());
        $this->view->assign('checkup', $checkup);
    }

}
