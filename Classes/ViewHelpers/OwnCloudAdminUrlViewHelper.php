<?php

namespace RKW\RkwCompetition\ViewHelpers;

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

use RKW\RkwCompetition\Api\OwnCloud;
use RKW\RkwCompetition\Utility\OwnCloudUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * Class OwnCloudAdminUrlViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class OwnCloudAdminUrlViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Initialize arguments.
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('register', '\RKW\RkwCompetition\Domain\Model\Register', 'The register object');
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     *
     * @return string
     * @throws Exception
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $register = $arguments['register'];
        if (!$register) {
            return '';
        }

        $folderPathArray = OwnCloudUtility::getFolderPathArray($register);

        // Use rawurlencode for the parts but keep slashes as is, or encode the whole path correctly
        // The requirement says: /rkw_competition/competition_uid_1/feuser_uid_81099_katha%2540gruene.com
        // Notice the double encoding of @ in the example: %2540 is %40 (encoded @) encoded again.

        $encodedPath = '';
        foreach ($folderPathArray as $index => $part) {
            // rawurlencode encodes '@' to '%40'.
            // The requirement says: /rkw_competition/competition_uid_1/feuser_uid_81099_katha%2540gruene.com
            // Notice the double encoding of @ in the example: %2540 is %40 (encoded @) encoded again.
            // But browser also encodes the link if we use it in href.

            $encodedPart = rawurlencode($part);
            $encodedPart = str_replace('%40', '%2540', $encodedPart);
            $encodedPath .= ($index === 0 ? '' : '/') . $encodedPart;
        }

        /** @var OwnCloud $ownCloudApi */
        $ownCloudApi = GeneralUtility::makeInstance(OwnCloud::class);
        $baseUrl = rtrim($ownCloudApi->getWebDavApi()->apiBaseUrl, '/');

        return $baseUrl . '/apps/files/?dir=' . $encodedPath;
    }
}
