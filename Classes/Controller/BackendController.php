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

use Madj2k\CoreExtended\Utility\GeneralUtility;
use RKW\RkwCompetition\Domain\Model\BackendUser;
use RKW\RkwCompetition\Domain\Model\Competition;
use RKW\RkwCompetition\Domain\Model\JuryReference;
use RKW\RkwCompetition\Domain\Model\Register;
use RKW\RkwCompetition\Domain\Repository\BackendUserRepository;
use RKW\RkwCompetition\Service\RkwMailService;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class BackendController
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendController extends \RKW\RkwCompetition\Controller\AbstractController
{

    /**
     * @var \RKW\RkwCompetition\Domain\Repository\BackendUserRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?BackendUserRepository $backendUserRepository = null;

    /**
     * @var \RKW\RkwCompetition\Domain\Repository\BackendUserRepository
     */
    public function injectBackendUserRepository(BackendUserRepository $backendUserRepository)
    {
        $this->backendUserRepository = $backendUserRepository;
    }


    /**
     * action list
     *
     * @return void
     */
    public function listAction(): void
    {
        // @toDo: Es werden nur Wettbewerbe angezeigt, für die die Abgabefrist noch nicht abgelaufen ist (siehe #4198 ) UND für die der BE-User als Admin eingetragen ist

        $this->view->assign('competitionList', $this->competitionRepository->findAll());
    }


    /**
     * action show
     *
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @return void
     */
    public function showAction(Competition $competition): void
    {

        // @toDo: Count FINISHED registrations by competition


        $registerList = $this->registerRepository->findByCompetition($competition);

        $this->view->assign('competition', $competition);
        $this->view->assign('registerList', $registerList);
        $this->view->assign('registerCountTotal', $registerList->count());
        $this->view->assign('finishedRegisterCount', $this->registerRepository->findApprovedByCompetition($competition)->count());
        $this->view->assign('refusedRegisterCount', $this->registerRepository->findRefusedByCompetition($competition)->count());


        // SPECIAL SOLUTION: Create FrontendLinks for Jury-Member
        \Madj2k\CoreExtended\Utility\FrontendSimulatorUtility::simulateFrontendEnvironment($this->settings['juryPid']);
        $juryMemberLinkArray = [];
        /** @var JuryReference $juryReference */
        foreach ($competition->getJuryMemberConfirmed() as $juryReference) {

            $juryLoginLink = $this->uriBuilder->reset()
                ->setArguments(
                    array('tx_rkwcompetition_jury' =>
                        array('userToken' => $juryReference->getGuestUser()->getUsername()),
                    )
                )
                ->setCreateAbsoluteUri(true)
                ->build();

            $juryMemberLinkArray[$juryReference->getEmail()] = $juryLoginLink;
        }
        //DebuggerUtility::var_dump($juryMemberLinkArray); exit;
        $this->view->assign('juryMemberLinkArray', $juryMemberLinkArray);
        \Madj2k\CoreExtended\Utility\FrontendSimulatorUtility::resetFrontendEnvironment();

    }


    /**
     * action registerDetail
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     */
    public function registerDetailAction(Register $register): void
    {
        //DebuggerUtility::var_dump($register->getUpload()->getAbstract()->getOriginalResource()->getOriginalFile()); exit;

        $this->view->assign('register', $register);
    }


    /**
     * action approve
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     */
    public function approveAction(Register $register): void
    {
        /** @var BackendUser $currentBackendUser */
        $currentBackendUser = $this->backendUserRepository->findByUid(intval($GLOBALS['BE_USER']->user['uid']));

        $register->setAdminApprovedBy($currentBackendUser);
        $register->setAdminApprovedAt(time());

        $this->registerRepository->update($register);

        $emailService = GeneralUtility::makeInstance(RkwMailService::class);
        $emailService->approvedRegisterUser($register->getFrontendUser(), $register);

        $this->addFlashMessage(
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'backendController.message.approved',
                'rkw_competition'
            )
        );

        $this->forward('registerDetail', null, null, ['register' => $register]);
    }



    /**
     * action refuse
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     */
    public function refuseAction(Register $register): void
    {

        /** @var BackendUser $currentBackendUser */
        $currentBackendUser = $this->backendUserRepository->findByUid(intval($GLOBALS['BE_USER']->user['uid']));

        $register->setAdminRefusedBy($currentBackendUser);
        $register->setAdminRefusedAt(time());

        $this->registerRepository->update($register);
        $this->persistenceManager->persistAll();

        $emailService = GeneralUtility::makeInstance(RkwMailService::class);
        $emailService->refusedRegisterUser($register->getFrontendUser(), $register);

        $this->addFlashMessage(
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'backendController.message.declined',
                'rkw_competition'
            )
        );

        $this->forward('registerDetail', null, null, ['register' => $register]);
    }


}
