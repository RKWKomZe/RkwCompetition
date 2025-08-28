<?php

namespace RKW\RkwCompetition\Utility;
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

use Brotkrueml\Schema\Model\Type\DanceEvent;
use Madj2k\CoreExtended\Utility\GeneralUtility;
use RKW\RkwCompetition\Domain\Model\Competition;
use RKW\RkwCompetition\Domain\Model\Register;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * FileUploadUtility
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FileUploadUtility
{
    /**
     * checkFileFormUpload
     *
     * checks if file exists
     *
     * @todo : check multiple upload array?
     *
     * @param array $fileFromForm The single file array
     * @return bool
     */
    public static function checkFileFormUpload(array $fileFromForm): bool
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
     * Returns the shortened mimetype (without "application/")
     *
     * @param array $file
     * @return string
     */
    public static function getShortenedMimeType(array $file) :string
    {
        $mimeTypeExplode = GeneralUtility::trimExplode('/', $file['type']);

        // return empty string if there is an issue
        return $mimeTypeExplode[1] ?: '';
    }



}
