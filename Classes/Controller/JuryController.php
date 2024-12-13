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
 *
 * @toDo: Gedanken:
 * Extra tabelle jury im nm-style wie sys_file_reference?
 * Hier Verbindung zwischen competition - frontendUser
 * Das sollte sich auch gut mit dem DoubleOptIn-Verfahren ergänzen
 * -> dazu Felder "reminder_mail" für besseres management
 * -> timestamp einverständniserklärung je wettbewerb
 *
 *
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
        $juryReferenceList = $this->juryReferenceRepository->findAll();
        $this->view->assign('juryReferenceList', $juryReferenceList);
    }



    /**
     * action show
     *
     * @deprecated Should not be used
     *
     * @param \RKW\RkwCompetition\Domain\Model\JuryReference $juryReference
     * @return string|object|null|void
     */
    public function showAction(\RKW\RkwCompetition\Domain\Model\JuryReference $juryReference)
    {
        $this->view->assign('juryReference', $juryReference);
    }



    /**
     * action new
     *
     * @deprecated Should not be used
     *
     * @return string|object|null|void
     */
    public function newAction()
    {
    }



    /**
     * action create
     *
     * @deprecated Should not be used
     *
     * @param \RKW\RkwCompetition\Domain\Model\juryReference $newjuryReference
     * @return string|object|null|void
     */
    public function createAction(\RKW\RkwCompetition\Domain\Model\juryReference $newjuryReference)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->juryReferenceRepository->add($newjuryReference);
        $this->redirect('list');
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
     *
     *
     * @param \RKW\RkwCompetition\Domain\Model\JuryReference $juryReference
     * @return string|object|null|void
     */
    public function updateAction(\RKW\RkwCompetition\Domain\Model\JuryReference $juryReference)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->juryReferenceRepository->update($juryReference);
        $this->redirect('list');
    }



    /**
     * action delete
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
