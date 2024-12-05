<?php
namespace RKW\RkwCompetition\Validation\Validator;

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

use Madj2k\CoreExtended\Utility\GeneralUtility as Common;
use RKW\RkwCompetition\Persistence\FileHandler;
use RKW\RkwCompetition\Utility\FileUploadUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class UploadValidator
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FileValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{

    /**
     * TypoScript Settings
     *
     * @var array
     */
    protected $settings = null;

    /**
     * validation
     *
     * @var \RKW\RkwEvents\Domain\Model\Register $register
     * @return bool
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function isValid($register): bool
    {

        // @toDo: Check word files for makros:
        // https://medium.com/nerd-for-tech/how-to-get-macro-info-from-a-docx-file-in-php-3ad18f49eee8


        $isValid = true;

        // initialize typoscript settings
        $this->getSettings();

        // get allowed file types
        $allowedFileTypes = GeneralUtility::trimExplode(",", $this->settings['upload']['allowedFileTypes']);

        // Abstract PDF
        if (FileUploadUtility::checkFileFormUpload($register->getUpload()->getFileAbstractUploadArray())) {

            $mimeType = FileUploadUtility::getShortenedMimeType($register->getUpload()->getFileAbstractUploadArray());

            if (!in_array($mimeType, $allowedFileTypes)) {

                $propertyName = LocalizationUtility::translate(
                    'tx_rkwcompetition_validator.upload.abstract',
                    'rkw_competition'
                );

                $this->result->forProperty('upload.abstract')->addError(
                    new Error(
                        LocalizationUtility::translate(
                            'tx_rkwcompetition_validator.wrong_mime_type',
                            'rkw_competition',
                            [$propertyName]
                        ), 1733373817
                    )
                );

                $isValid = false;
            }
        }

        // Full PDF
        if (FileUploadUtility::checkFileFormUpload($register->getUpload()->getFileFullUploadArray())) {
            $mimeType = FileUploadUtility::getShortenedMimeType($register->getUpload()->getFileFullUploadArray());

            if (!in_array($mimeType, $allowedFileTypes)) {

                $propertyName = LocalizationUtility::translate(
                    'tx_rkwcompetition_validator.upload.full',
                    'rkw_competition'
                );

                $this->result->forProperty('upload.full')->addError(
                    new Error(
                        LocalizationUtility::translate(
                            'tx_rkwcompetition_validator.wrong_mime_type',
                            'rkw_competition',
                            [$propertyName]
                        ), 1733373817
                    )
                );

                $isValid = false;
            }
        }

        return $isValid;
    }


    /**
     * Returns TYPO3 settings
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function getSettings(): array
    {

        if (!$this->settings) {
            $this->settings = Common::getTypoScriptConfiguration('Rkwcompetition');
        }

        if (!$this->settings) {
            return [];
        }
        //===

        return $this->settings;
        //===
    }

}

