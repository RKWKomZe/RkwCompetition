<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Controller;


use RKW\RkwCompetition\Persistence\FileHandler;
use Solarium\Component\Debug;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
 * UploadController
 */
class UploadController extends \RKW\RkwCompetition\Controller\AbstractController
{
    /**
     * uploadRepository
     *
     * @var \RKW\RkwCompetition\Domain\Repository\UploadRepository
     */
    protected $uploadRepository = null;

    /**
     * fileRepository
     *
     * @var \RKW\RkwCompetition\Domain\Repository\FileRepository
     */
    protected $fileRepository = null;

    /**
     * fileReferenceRepository
     *
     * @var \RKW\RkwCompetition\Domain\Repository\FileReferenceRepository
     */
    protected $fileReferenceRepository = null;

    /**
     * @param \RKW\RkwCompetition\Domain\Repository\UploadRepository $uploadRepository
     */
    public function injectUploadRepository(\RKW\RkwCompetition\Domain\Repository\UploadRepository $uploadRepository)
    {
        $this->uploadRepository = $uploadRepository;
    }

    /**
     * @param \RKW\RkwCompetition\Domain\Repository\FileRepository $fileRepository
     */
    public function injectFileRepository(\RKW\RkwCompetition\Domain\Repository\FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param \RKW\RkwCompetition\Domain\Repository\FileReferenceRepository $fileReferenceRepository
     */
    public function injectFileReferenceRepository(\RKW\RkwCompetition\Domain\Repository\FileReferenceRepository $fileReferenceRepository)
    {
        $this->fileReferenceRepository = $fileReferenceRepository;
    }


    /**
     * action edit
     *
     * -> upload is part of register. So we're technically editing the register object here
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("register")
     * @return void
     */
    public function editAction(\RKW\RkwCompetition\Domain\Model\Register $register)
    {
        $this->view->assign('register', $register);
    }



    /**
     * action update
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("register")
     * @return void
     */
    public function updateAction(\RKW\RkwCompetition\Domain\Model\Register $register)
    {

        DebuggerUtility::var_dump($register); exit;

        // @toDo: Validation mimeType stuff
        // https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/13.3/Feature-103511-IntroduceExtbaseFileUploadHandling.html#83749-validationkeys

        /** @var \RKW\RkwCompetition\Persistence\FileHandler $fileHandler */
        $fileHandler = GeneralUtility::makeInstance(FileHandler::class);

        // set special file path for every frontendUser
        $fileHandler->setSubFolderName($register->getUniqueId());


        // @toDo: FileHandler try-catch

        // Abstract PDF
        if ($fileHandler->checkFileFormUpload($register->getUpload()->getFileAbstractUploadArray())) {
            $register->getUpload()->setAbstract($fileHandler->importUploadedResource($register->getUpload()->getFileAbstractUploadArray()));
        }

        // Full PDF
        if ($fileHandler->checkFileFormUpload($register->getUpload()->getFileFullUploadArray())) {
            $register->getUpload()->setFull($fileHandler->importUploadedResource($register->getUpload()->getFileFullUploadArray()));
        }

        $this->addFlashMessage('The object was created.');
        $this->registerRepository->update($register);


        $this->uploadRepository->update($register->getUpload());

        $this->redirect('edit', 'Upload', null, ['register' => $register]);
    }



    /**
     * action delete
     *
     * -> because cascadeRemove in model does not work on edit with null (via checkbox)
     *
     * @param \RKW\RkwCompetition\Domain\Model\Upload $upload
     * @param string $property
     * @param \RKW\RkwCompetition\Domain\Model\FileReference $fileReference
     * @return string|object|null|void
     */
    public function deleteAction(
        \RKW\RkwCompetition\Domain\Model\Register $register,
        string $property,
        \RKW\RkwCompetition\Domain\Model\FileReference $fileReference
    )
    {

        $unset = 'unset' . ucfirst($property);
        $register->getUpload()->$unset();

        /** @var \RKW\RkwCompetition\Persistence\FileHandler $fileHandler */
        $fileHandler = GeneralUtility::makeInstance(FileHandler::class);

        // remove file from HDD
    //    $fileHandler->removeFileFromHdd($fileReference->getFile());

        // remove file from repo
        $this->fileRepository->remove($fileReference->getFile());

        // remove fileReference from repo (with "cascadeRemove")
        $this->fileReferenceRepository->remove($fileReference);


        $this->addFlashMessage('The file was deleted.');
        $this->redirect('edit', 'Upload', null, ['register' => $register]);
    }


}
