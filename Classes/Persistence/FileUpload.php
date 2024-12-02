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
class FileUpload implements SingletonInterface
{

    /**
     * @var \TYPO3\CMS\Core\Log\Logger|null
     */
    protected ?Logger $logger = null;


    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceFactory|null
     */
    protected ?ResourceFactory $resourceFactory = null;

    /**
     * @var string
     */
    protected string $defaultUploadFolder = '1:/user_upload/tx_rkwcompetition';


    /**
     * One of 'cancel', 'replace', 'rename'
     *
     * @var string
     */
    protected string $defaultConflictMode = 'rename';


    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface|null
     */
    protected ?ObjectManagerInterface $objectManager = null;


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager|null
     */
    protected ?PersistenceManager $persistenceManager = null;


    /**
     * @return void
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function __construct()
    {
        /** @var \TYPO3\CMS\Core\Resource\ResourceFactory $resourceFactory */
        $this->resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager */
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager */
        $this->persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);

    }



    /**
     * checkFileFormUpload
     *
     * @todo : check multiple upload arry?
     *
     * @param array $fileFromForm The single file array
     * @return bool
     */
    public function checkFileFormUpload(array $fileFromForm): bool
    {
        // check if file upload exists (4 = no file set)
        if ($fileFromForm["error"] == 4) {
            return false;
        }

        // Otherwise return true. File is part of upload
        return true;
    }



    /**
     * importUploadedResource
     *
     * @param array $file
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException|Exception
     */
    public function importUploadedResource(array $file): ExtbaseFileReference
    {
        $uploadFolder = $this->resourceFactory->retrieveFileOrFolderObject($this->defaultUploadFolder);
        $uploadedFile = $uploadFolder->addUploadedFile($file, $this->defaultConflictMode);  //  second parameter $conflict mode

        return $this->createFileReferenceFromFalFileObject($uploadedFile);
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
    public static function getSettings(string $which = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS): array
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

}
