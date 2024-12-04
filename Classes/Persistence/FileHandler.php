<?php
namespace RKW\RkwCompetition\Persistence;

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

use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\FileReference as CoreFileReference;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Domain\Model\FileReference as ExtbaseFileReference;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class UploadHelper
 *
 * @todo: This should be part of a central extension. We only would need getter and setter for the uploadFolder and maybe settings
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @author Christian Dilger <c.dilger@addorange.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FileHandler implements SingletonInterface
{

    /*
     * @var string
     */
    protected string $subFolderName = '';

    /**
     * @var string
     */
    protected string $defaultUploadFolder = 'user_upload/tx_rkwcompetition';

    /**
     * @var string
     */
    protected string $resourceStorageUid = '1';

    /**
     * One of 'cancel', 'replace', 'rename'
     *
     * @var string
     */
    protected string $defaultConflictMode = 'rename';

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceFactory|null
     */
    protected ?ResourceFactory $resourceFactory = null;

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceStorage|null
     */
    protected ?ResourceStorage $resourceStorage = null;

    /**
     * @var \TYPO3\CMS\Core\Resource\Folder|null
     */
    protected ?Folder $folder = null;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface|null
     */
    protected ?ObjectManagerInterface $objectManager = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager|null
     */
    protected ?PersistenceManager $persistenceManager = null;

    /**
     * @var \TYPO3\CMS\Core\Log\Logger|null
     */
    protected ?Logger $logger = null;

    /**
     * @return void
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function __construct()
    {
        /** @var \TYPO3\CMS\Core\Resource\ResourceFactory $resourceFactory */
        $this->resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);

        /** @var \TYPO3\CMS\Core\Resource\ResourceStorage $resourceStorage */
        $this->resourceStorage = $this->resourceFactory->getStorageObject($this->resourceStorageUid);

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager */
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager */
        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
    }



    /**
     * checkFileFormUpload
     *
     * @todo : check multiple upload array?
     *
     * @param array $fileFromForm The single file array
     * @return bool
     */
    public function checkFileFormUpload(array $fileFromForm): bool
    {
        // check if file upload exists (4 = no file set)
        if (
            empty($fileFromForm)
            || $fileFromForm["error"] == 4
        ) {
            return false;
        }

        // Otherwise return true. File is part of upload
        return true;
    }



    /**
     * importUploadedResource
     *
     * @param array $fileUpload
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException|Exception
     */
    public function importUploadedResource(array $fileUpload): ExtbaseFileReference
    {
        // get the basic folder
        $this->folder = $this->resourceFactory->createFolderObject(
            $this->resourceStorage,
            $this->defaultUploadFolder,
            $this->getDefaultUploadFolder()
        );

        // If given: Get the subFolder. Create it, if necessary
        $folder = $this->createNewSubFolderIfNotExists();

        // upload into the given folder
        $uploadedFile = $folder->addUploadedFile($fileUpload, $this->defaultConflictMode);

        return $this->createFileReferenceFromFalFileObject($uploadedFile);
    }



    /**
     * removeFileFromHdd
     *
     * @param \Madj2k\CoreExtended\Domain\Model\File $file
     * @return bool
     */
    public function removeFileFromHdd(\Madj2k\CoreExtended\Domain\Model\File $file): bool
    {
        return $this->resourceStorage->deleteFile($file->get);
    }



    /**
     * removeFolderFromHddByIdentifier
     *
     * -> folder name is the identifier of a file (the path)
     *
     * @param string $folderIdentifier
     * @return bool
     */
    public function removeFolderFromHddByIdentifier(string $folderIdentifier): bool
    {
        $folderToRemove = $this->resourceFactory->createFolderObject(
            $this->resourceStorage,
            $folderIdentifier,
            $folderIdentifier
        );

        return $this->removeFolderFromHddByFolder($folderToRemove);
    }



    /**
     * removeFolderFromHddByFolder
     *
     * @param \TYPO3\CMS\Core\Resource\Folder $folder
     * @return bool
     */
    public function removeFolderFromHddByFolder(\TYPO3\CMS\Core\Resource\Folder $folder): bool
    {
        return $this->resourceStorage->deleteFolder($folder);
    }



    /**
     * createNewFileFolder
     *
     * @return \TYPO3\CMS\Core\Resource\Folder
     */
    protected function createNewSubFolderIfNotExists(): Folder
    {
        // if no subFolder is given, do nothing. Return current folder
        if (!$this->subFolderName) {
            return $this->folder;
        }

        // if subFolder already exists, return subFolder.
        if ($this->folder->hasFolder($this->subFolderName)) {
            return $this->resourceFactory->createFolderObject(
                $this->resourceStorage,
                $this->getFullFilePath(),
                $this->subFolderName
            );
        }

        // else: Create and return new folder
        return $this->folder->createFolder($this->getSubFolderName());
    }



    /**
     * @param \TYPO3\CMS\Core\Resource\File $file
     * @param int|null $resourcePointer
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @throws Exception
     */
    protected function createFileReferenceFromFalFileObject(
        File $file,
        int $resourcePointer = null
    ): ExtbaseFileReference {

        $fileReference = $this->resourceFactory->createFileReferenceObject(
            [
                'uid_local' => $file->getUid(),
                'uid_foreign' => uniqid('NEW_'),
                'uid' => uniqid('NEW_'),
                'crop' => null,
            ]
        );
        return $this->createFileReferenceFromFalFileReferenceObject($fileReference, $resourcePointer);
    }



    /**
     * In case no $resourcePointer is given a new file reference domain object
     * will be returned. Otherwise, the file reference is reconstituted from
     * storage and will be updated(!) with the provided $falFileReference.
     *
     * @param \TYPO3\CMS\Core\Resource\FileReference $falFileReference
     * @param int|null $resourcePointer
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @throws Exception
     */
    protected function createFileReferenceFromFalFileReferenceObject(
        CoreFileReference $falFileReference,
        int $resourcePointer = null
    ): ExtbaseFileReference {

        if ($resourcePointer === null) {
            /** @var ExtbaseFileReference $fileReference */
            $fileReference = $this->objectManager->get(ExtbaseFileReference::class);
        } else {
            /** @var ExtbaseFileReference $fileReference */
            $fileReference = $this->persistenceManager->getObjectByIdentifier(
                $resourcePointer,
                ExtbaseFileReference::class,
                false
            );
        }

        $fileReference->setOriginalResource($falFileReference);
        return $fileReference;
    }



    /**
     * Returns TYPO3 settings
     *
     * @param string $which Which type of settings will be loaded
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected static function getSettings(string $which = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS): array
    {
        return \Madj2k\CoreExtended\Utility\GeneralUtility::getTypoScriptConfiguration('Rkwcompetition', $which);
    }



    /**
     * Returns logger instance
     *
     * @return Logger
     */
    protected function getLogger(): Logger
    {
        if (!$this->logger instanceof Logger) {
            $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        }

        return $this->logger;
    }



    /**
     * @return string
     */
    public function getSubFolderName(): string
    {
        return $this->subFolderName;
    }



    /**
     * @param string $subFolderName The path without beginning or ending slash
     * @return void
     */
    public function setSubFolderName(string $subFolderName): void
    {
        $this->subFolderName = rtrim(ltrim($subFolderName, '/'), '/');
    }

    /**
     * @return string
     */
    public function getResourceStorageUid(): string
    {
        return $this->resourceStorageUid;
    }

    /**
     * @param string $resourceStorageUid
     * @return void
     */
    public function setResourceStorageUid(string $resourceStorageUid): void
    {
        $this->resourceStorageUid = $resourceStorageUid;

        // if new storage UID is set: Set new storage
        /** @var \TYPO3\CMS\Core\Resource\ResourceStorage $resourceStorage */
        $this->resourceStorage = $this->resourceFactory->getStorageObject($this->resourceStorageUid);
    }

    /**
     * @return string
     */
    public function getDefaultUploadFolder(): string
    {
        return $this->defaultUploadFolder;
    }

    /**
     * @param string $defaultUploadFolder
     * @return void
     */
    public function setDefaultUploadFolder(string $defaultUploadFolder): void
    {
        $this->defaultUploadFolder = $defaultUploadFolder;
    }

    /**
     *
     * @return string
     */
    private function getFullFilePath(): string
    {
        return $this->defaultUploadFolder . '/' . $this->subFolderName . '/';
    }

}
