<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Controller;


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
 *
 * JuryController
 */
class JuryController extends \RKW\RkwCompetition\Controller\AbstractController
{

    /**
     * juryReferenceRepository
     *
     * @var \RKW\RkwCompetition\Domain\Repository\JuryReferenceRepository
     */
    protected $juryReferenceRepository = null;


    /**
     * @param \RKW\RkwCompetition\Domain\Repository\JuryReferenceRepository $juryReferenceRepository
     */
    public function injectJuryReferenceRepository(\RKW\RkwCompetition\Domain\Repository\JuryReferenceRepository $juryReferenceRepository)
    {
        $this->juryReferenceRepository = $juryReferenceRepository;
    }



    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {

        if (!$this->getFrontendUser()) {

            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                    'participantController.message.error.notPermitted',
                    'rkw_competition'
                ),
                '',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
        } else {

            $juryReferenceList = $this->juryReferenceRepository->findByFrontendUser($this->getFrontendUser());

            $this->view->assign('juryReferenceList', $juryReferenceList);
        }
    }



    /**
     * action show
     *
     * @param \RKW\RkwCompetition\Domain\Model\JuryReference $juryReference
     * @return string|object|null|void
     */
    public function showAction(\RKW\RkwCompetition\Domain\Model\JuryReference $juryReference)
    {
        $this->view->assign('juryReference', $juryReference);
        $this->view->assign(
            'approvedRegistrations',
            $this->registerRepository->findAdminApprovedByCompetition($juryReference->getCompetition())
        );
    }



    /**
     * action edit
     *
     * Hint: The juryReference record is created via cronjob (juryNotify)
     *
     * @param \RKW\RkwCompetition\Domain\Model\JuryReference $juryReference
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("juryReference")
     * @return string|object|null|void
     */
    public function editAction(\RKW\RkwCompetition\Domain\Model\JuryReference $juryReference)
    {
        $this->view->assign('juryReference', $juryReference);
    }



    /**
     * action update
     *
     * @param \RKW\RkwCompetition\Domain\Model\JuryReference $juryReference
     * @param int $consentAsJuryMember
     * @return string|object|null|void
     */
    public function updateAction(
        \RKW\RkwCompetition\Domain\Model\JuryReference $juryReference,
        int $consentAsJuryMember = 0
    )
    {

        $errorMessage = '';

        if (!$consentAsJuryMember) {
            $errorMessage = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'juryController.message.consent',
                'rkw_competition'
            );

        }
        if ($juryReference->getConsentedAt()) {
            $errorMessage = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'juryController.message.alreadyConsented',
                'rkw_competition'
            );
        }

        if ($errorMessage) {
            $this->addFlashMessage(
                $errorMessage,
                '',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );

            $this->redirect(
                'edit',
                null,
                null,
                ['juryReference' => $juryReference]
            );
        }

        $juryReference->setConsentedAt(time());

        $this->addFlashMessage(
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'juryController.message.updated',
                'rkw_competition'
            )
        );
        $this->juryReferenceRepository->update($juryReference);
        $this->redirect('list');
    }



    /**
     * action delete
     *
     * @deprecated Not used yet
     *
     * @todo Bisher keine Vorgabe, dass Jurymitglieder sich selbst rauslöschen können. Könnte chaotisch werden!
     *
     * @param \RKW\RkwCompetition\Domain\Model\JuryReference $juryReference
     * @return string|object|null|void
     */
    public function deleteAction(\RKW\RkwCompetition\Domain\Model\JuryReference $juryReference)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->juryReferenceRepository->remove($juryReference);
        $this->redirect('list');
    }
}
