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
use RKW\RkwCompetition\Api\OwnCloud;
use RKW\RkwCompetition\Domain\Model\BackendUser;
use RKW\RkwCompetition\Domain\Model\Competition;
use RKW\RkwCompetition\Domain\Model\JuryReference;
use RKW\RkwCompetition\Domain\Model\Register;
use RKW\RkwCompetition\Domain\Repository\BackendUserRepository;
use RKW\RkwCompetition\Service\RkwMailService;
use RKW\RkwCompetition\Utility\RegisterUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
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
     * initializeAction
     *
     * @return void
     */
    public function initializeAction(): void
    {
        parent::initializeAction();
    }


    /**
     * Checks if a complete connection to the OwnCloud instance can be established.
     *
     * @return void
     */
    protected function checkOwnCloudConnection(): void
    {
        try {
            $ownCloud = GeneralUtility::makeInstance(OwnCloud::class);
            $shareApi = $ownCloud->getShareApi();

            if (isset($this->settings['api']['ownCloud']['baseUrl']) && (string)$this->settings['api']['ownCloud']['baseUrl'] !== '') {
                $shareApi->apiBaseUrl = (string)$this->settings['api']['ownCloud']['baseUrl'];
            }
            if (isset($this->settings['api']['ownCloud']['admin']['username']) && (string)$this->settings['api']['ownCloud']['admin']['username'] !== '') {
                $shareApi->apiAdminUsername = (string)$this->settings['api']['ownCloud']['admin']['username'];
            }
            if (isset($this->settings['api']['ownCloud']['admin']['password']) && (string)$this->settings['api']['ownCloud']['admin']['password'] !== '') {
                $shareApi->apiAdminPassword = (string)$this->settings['api']['ownCloud']['admin']['password'];
            }

            $baseUrl = (string)($shareApi->apiBaseUrl ?? '');
            if ($baseUrl === '') {
                $this->addFlashMessage(
                    'OwnCloud-Verbindungsfehler: Die apiBaseUrl ist nicht konfiguriert.',
                    'OwnCloud',
                    AbstractMessage::ERROR
                );
                return;
            }

            // Test API request (using ShareApi via AbstractApi, does not require admin privileges)
            $result = $shareApi->getAllShares();
            if (isset($result['status']) && $result['status'] === 'failure') {
                $errorMsg = 'OwnCloud-Verbindungsfehler (Base URL: ' . $baseUrl . '): API-Fehler: ' . ($result['message'] ?? json_encode($result));
                $this->addFlashMessage($errorMsg, 'OwnCloud', AbstractMessage::ERROR);
                return;
            }
            if (isset($result['error'])) {
                $errorDetails = 'OwnCloud-Verbindungsfehler (Base URL: ' . $baseUrl . '): API-Fehler: ' . $result['error'];
                if (isset($result['http_code'])) {
                    $errorDetails .= ' [HTTP Status: ' . $result['http_code'] . ']';
                }
                if (!empty($result['raw'])) {
                    $rawSnippet = trim(strip_tags($result['raw']));
                    if ($rawSnippet === '') {
                        $rawSnippet = trim($result['raw']);
                    }
                    if (mb_strlen($rawSnippet) > 300) {
                        $rawSnippet = mb_substr($rawSnippet, 0, 300) . '...';
                    }
                    $errorDetails .= ' | Response: ' . $rawSnippet;
                }
                $this->addFlashMessage($errorDetails, 'OwnCloud', AbstractMessage::ERROR);
                return;
            }

            $this->addFlashMessage(
                'Verbindung zur OwnCloud-Instanz erfolgreich hergestellt.',
                'OwnCloud',
                AbstractMessage::OK
            );

        } catch (\Throwable $e) {
            $this->addFlashMessage(
                'OwnCloud-Verbindungsfehler (Base URL: ' . ($baseUrl ?? 'unbekannt') . '): ' . $e->getMessage(),
                'OwnCloud',
                AbstractMessage::ERROR
            );
        }
    }


    /**
     * action list
     *
     * @return void
     */
    public function listAction(): void
    {
        // @toDo: Es werden nur Wettbewerbe angezeigt, für die die Abgabefrist noch nicht abgelaufen ist (siehe #4198 ) UND für die der BE-User als Admin eingetragen ist
        $this->checkOwnCloudConnection();

        $this->view->assign('action', 'list');
        $this->view->assign('competitionList', $this->competitionRepository->findAll());
    }


    /**
     * action show
     *
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @param int $filterStatus
     * @return void
     */
    public function showAction(Competition $competition, int $filterStatus = 0): void
    {

        // @toDo: Count FINISHED registrations by competition

        $this->view->assign('action', 'show');
        $registerList = $this->registerRepository->findByCompetition($competition);

        $this->view->assign('competition', $competition);
        $this->view->assign('registerCountTotal', $registerList->count());
        $this->view->assign('newRegisterCount', $this->registerRepository->findNewByCompetition($competition)->count());
        $this->view->assign('finishedRegisterCount', $this->registerRepository->findApprovedByCompetition($competition)->count());
        $this->view->assign('refusedRegisterCount', $this->registerRepository->findRefusedByCompetition($competition)->count());
        $this->view->assign('returnedRegisterCount', $this->registerRepository->findReturnedByCompetition($competition)->count());
        $this->view->assign('notApprovedRegisterCount', $this->registerRepository->findNotApprovedByCompetition($competition)->count());

        $sectorOverview = [];
        if ($competition->getSectors()) {
            foreach ($competition->getSectors() as $sector) {
                $sectorOverview[] = [
                    'sector' => $sector,
                    'count' => $this->registerRepository->findAdminApprovedByCompetition($competition, $sector)->count()
                ];
            }
        }
        $this->view->assign('sectorOverview', $sectorOverview);

        $filteredRegisterList = [];
        foreach ($registerList as $register) {
            if ($filterStatus > 0) {
                if (RegisterUtility::registerStatus($register) === $filterStatus) {
                    $filteredRegisterList[] = $register;
                }
            } else {
                $filteredRegisterList[] = $register;
            }
        }

        $this->view->assign('registerList', $filteredRegisterList);
        $this->view->assign('filterStatus', $filterStatus);


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

        $this->view->assign('action', 'registerDetail');
        $this->view->assign('competition', $register->getCompetition());
        $this->view->assign('register', $register);
    }


    /**
     * action processStatus
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @param int $adminStatus
     * @param string $adminComment
     * @return void
     */
    public function processStatusAction(Register $register, int $adminStatus, string $adminComment = ''): void
    {
        /** @var BackendUser $currentBackendUser */
        $currentBackendUser = $this->backendUserRepository->findByUid(intval($GLOBALS['BE_USER']->user['uid']));
        $emailService = GeneralUtility::makeInstance(RkwMailService::class);

        switch ($adminStatus) {
            case RegisterUtility::STATUS_APPROVED:
                $register->setAdminApprovedBy($currentBackendUser);
                $register->setAdminApprovedAt(time());
                // Reset others just in case
                $register->setAdminRefusedAt(0);
                $register->setAdminReturnedAt(0);

                $this->registerRepository->update($register);
                $this->persistenceManager->persistAll();
                $emailService->approvedRegisterUser($register->getFrontendUser(), $register);

                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'backendController.message.approved',
                        'rkw_competition'
                    )
                );
                break;

            case RegisterUtility::STATUS_REFUSED:
                $register->setAdminRefusedBy($currentBackendUser);
                $register->setAdminRefusedAt(time());
                $register->setAdminRefusedText($adminComment);
                // Reset others
                $register->setAdminApprovedAt(0);
                $register->setAdminReturnedAt(0);

                $this->registerRepository->update($register);
                $this->persistenceManager->persistAll();
                $emailService->refusedRegisterUser($register->getFrontendUser(), $register);

                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'backendController.message.declined',
                        'rkw_competition'
                    )
                );
                break;

            case RegisterUtility::STATUS_RETURNED:
                $register->setAdminReturnedBy($currentBackendUser);
                $register->setAdminReturnedAt(time());
                $register->setAdminReturnedText($adminComment);
                // Reset others
                $register->setAdminApprovedAt(0);
                $register->setAdminRefusedAt(0);

                // IMPORTANT: Allow user to edit again by resetting submission timestamp
                $register->setUserSubmittedAt(0);

                $this->registerRepository->update($register);
                $this->persistenceManager->persistAll();

                // We need to implement this method in MailService
                $emailService->returnedRegisterUser($register->getFrontendUser(), $register);

                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'backendController.message.returned',
                        'rkw_competition'
                    )
                );
                break;
        }

        $this->forward('registerDetail', null, null, ['register' => $register]);
    }
}
