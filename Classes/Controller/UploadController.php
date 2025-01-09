<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Controller;


use RKW\RkwCompetition\Domain\Model\Upload;
use RKW\RkwCompetition\Persistence\FileHandler;
use RKW\RkwCompetition\Utility\FileUploadUtility;
use Solarium\Component\Debug;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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
     * @var \Madj2k\CoreExtended\Domain\Repository\FileRepository
     */
    protected $fileRepository = null;

    /**
     * fileReferenceRepository
     *
     * @var \Madj2k\CoreExtended\Domain\Repository\FileReferenceRepository
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
     * @param \Madj2k\CoreExtended\Domain\Repository\FileRepository $fileRepository
     */
    public function injectFileRepository(\Madj2k\CoreExtended\Domain\Repository\FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param \Madj2k\CoreExtended\Domain\Repository\FileReferenceRepository $fileReferenceRepository
     */
    public function injectFileReferenceRepository(\Madj2k\CoreExtended\Domain\Repository\FileReferenceRepository $fileReferenceRepository)
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
     * @throws AspectNotFoundException
     */
    public function editAction(\RKW\RkwCompetition\Domain\Model\Register $register)
    {
        // check if "Upload"-Entity exists
        if (!$register->getUpload() instanceof Upload) {

           // throw new AspectNotFoundException();

            // instead of throwing error simply create and add an Upload object
            $upload = GeneralUtility::makeInstance(Upload::class);
            $register->setUpload($upload);
            $this->registerRepository->update($register);
            $this->persistenceManager->persistAll();
        }

        $this->view->assign('register', $register);
    }



    /**
     * action update
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @TYPO3\CMS\Extbase\Annotation\Validate("RKW\RkwCompetition\Validation\Validator\FileValidator", param="register")
     * @return void
     */
    public function updateAction(\RKW\RkwCompetition\Domain\Model\Register $register)
    {

        // @toDo: Validation mimeType stuff
        // https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/13.3/Feature-103511-IntroduceExtbaseFileUploadHandling.html#83749-validationkeys

        /** @var \RKW\RkwCompetition\Persistence\FileHandler $fileHandler */
        $fileHandler = GeneralUtility::makeInstance(FileHandler::class);

        // @toDo: Warning if no subFolderName is given? (no "uniqueId")

        // set special file path for every frontendUser
        $fileHandler->setSubFolderName($register->getUniqueId());

        // @toDo: FileHandler try-catch

        $uploadCounter = 0;

        // Abstract PDF
        if (FileUploadUtility::checkFileFormUpload($register->getUpload()->getFileAbstractUploadArray())) {
            $register->getUpload()->setAbstract($fileHandler->importUploadedResource($register->getUpload()->getFileAbstractUploadArray()));
            $uploadCounter++;
        }

        // Full PDF
        if (FileUploadUtility::checkFileFormUpload($register->getUpload()->getFileFullUploadArray())) {
            $register->getUpload()->setFull($fileHandler->importUploadedResource($register->getUpload()->getFileFullUploadArray()));
            $uploadCounter++;
        }

        $this->addFlashMessage(
            LocalizationUtility::translate(
                'updateController.message.uploadSuccess',
                'rkw_competition'
            )
        );

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
     * @param \Madj2k\CoreExtended\Domain\Model\FileReference $fileReference
     * @return string|object|null|void
     */
    public function deleteAction(
        \RKW\RkwCompetition\Domain\Model\Register $register,
        string $property,
        \Madj2k\CoreExtended\Domain\Model\FileReference $fileReference
    )
    {

        $unset = 'unset' . ucfirst($property);
        $register->getUpload()->$unset();

        /** @var \RKW\RkwCompetition\Persistence\FileHandler $fileHandler */
        $fileHandler = GeneralUtility::makeInstance(FileHandler::class);

        // remove file from HDD
        $fileHandler->removeFileFromHdd($fileReference->getFile());

        // remove file from repo
        $this->fileRepository->remove($fileReference->getFile());

        // remove fileReference from repo (with "cascadeRemove")
        $this->fileReferenceRepository->remove($fileReference);

        $this->addFlashMessage(
            LocalizationUtility::translate('updateController.message.fileDeleted')
        );
        $this->addFlashMessage('The file was deleted.');

        $this->redirect('edit', 'Upload', null, ['register' => $register]);
    }


}
