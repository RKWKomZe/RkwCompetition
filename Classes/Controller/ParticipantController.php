<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Controller;


use Madj2k\FeRegister\Domain\Model\FrontendUser;
use Madj2k\FeRegister\Utility\FrontendUserSessionUtility;
use Madj2k\FeRegister\Utility\FrontendUserUtility;
use RKW\RkwCompetition\Utility\RegisterUtility;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
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
 * ParticipantController
 */
class ParticipantController extends \RKW\RkwCompetition\Controller\AbstractController
{

    /**
     * action list
     *
     * @return void
     * @throws AspectNotFoundException
     */
    public function listAction()
    {

        if (!$this->getFrontendUser()) {

            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                    'participantController.message.error.notPermitted', 'rkw_competition'
                ),
                '',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
        } else {

            $registerList = $this->registerRepository->findByFrontendUser($this->getFrontendUser());

            $this->view->assign('registerList', $registerList);
        }

    }



    /**
     * action show
     *
     * @param \RKW\RkwCompetition\Domain\Model\Upload $upload
     * @return string|object|null|void
     */
    public function showAction(\RKW\RkwCompetition\Domain\Model\Upload $upload)
    {
        $this->view->assign('upload', $upload);
    }



    /**
     * action new
     *
     * @return string|object|null|void
     */
    public function newAction()
    {
    }



    /**
     * action create
     *
     * @param \RKW\RkwCompetition\Domain\Model\Upload $newUpload
     * @return string|object|null|void
     */
    public function createAction(\RKW\RkwCompetition\Domain\Model\Upload $newUpload)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->uploadRepository->add($newUpload);
        $this->redirect('list');
    }



    /**
     * action edit
     *
     * @param \RKW\RkwCompetition\Domain\Model\Upload $upload
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("upload")
     * @return string|object|null|void
     */
    public function editAction(\RKW\RkwCompetition\Domain\Model\Upload $upload)
    {
        $this->view->assign('upload', $upload);
    }



    /**
     * action update
     *
     * @param \RKW\RkwCompetition\Domain\Model\Upload $upload
     * @return string|object|null|void
     */
    public function updateAction(\RKW\RkwCompetition\Domain\Model\Upload $upload)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->uploadRepository->update($upload);
        $this->redirect('list');
    }



    /**
     * action delete
     *
     * @param \RKW\RkwCompetition\Domain\Model\Upload $upload
     * @return string|object|null|void
     */
    public function deleteAction(\RKW\RkwCompetition\Domain\Model\Upload $upload)
    {
        $this->addFlashMessage('The object was deleted.');
        $this->uploadRepository->remove($upload);
        $this->redirect('list');
    }



}
