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
 * Class OwnCloudFilesListViewHelper
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class OwnCloudFilesListViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper
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
     * @return array
     * @throws Exception
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $register = $arguments['register'];
        if (!$register || !$register->getFrontendUser() || !$register->getCompetition()) {
            return [];
        }

        $folderPathArray = OwnCloudUtility::getFolderPathArray($register);

        /** @var OwnCloud $ownCloudApi */
        $ownCloudApi = GeneralUtility::makeInstance(OwnCloud::class);
        $files = $ownCloudApi->getWebDavApi()->getFilesWithDetails($folderPathArray);

        if (empty($files)) {
            return [];
        }

        // Fetch existing shares for this folder to reuse them and avoid duplicates
        $existingShares = $ownCloudApi->getShareApi()->getAllShares(
            $folderPathArray,
            'json',
            true,
            '',
            '',
            true
        );

        $existingSharesByPath = [];
        if (!empty($existingShares) && is_array($existingShares)) {
            foreach ($existingShares as $shareData) {
                $shareType = (int)($shareData['element']['share_type'] ?? $shareData['share_type'] ?? $shareData['shareType'] ?? 0);
                if ($shareType === 3) {
                    $sharePath = $shareData['element']['path'] ?? $shareData['path'] ?? $shareData['file_target'] ?? '';
                    $shareUrl = $shareData['element']['url'] ?? $shareData['url'] ?? '';
                    if ($sharePath && $shareUrl) {
                        $normalizedPath = '/' . trim($sharePath, '/');
                        $existingSharesByPath[$normalizedPath] = $shareUrl;
                        $existingSharesByPath[basename($normalizedPath)] = $shareUrl;
                    }
                }
            }
        }

        $result = [];
        $expireDate = (new \DateTime('+1 day'))->format('Y-m-d');

        foreach ($files as $fileData) {
            $file = is_array($fileData) ? ($fileData['name'] ?? '') : $fileData;
            $sizeInBytes = is_array($fileData) ? (int)($fileData['size'] ?? 0) : 0;
            $sizeInMb = $sizeInBytes > 0 ? max(0.01, round($sizeInBytes / (1024 * 1024), 2)) : 0;

            $filePathArray = array_merge($folderPathArray, [$file]);
            $fullPath = '/' . trim(implode('/', $filePathArray), '/');

            $downloadUrl = '';
            if (isset($existingSharesByPath[$fullPath])) {
                $downloadUrl = rtrim($existingSharesByPath[$fullPath], '/') . '/download';
            } elseif (isset($existingSharesByPath[$file])) {
                $downloadUrl = rtrim($existingSharesByPath[$file], '/') . '/download';
            } else {
                try {
                    $newShare = $ownCloudApi->getShareApi()->createShare(
                        $file,
                        $filePathArray,
                        3,
                        '',
                        false,
                        '',
                        1,
                        $expireDate
                    );

                    $shareUrl = $newShare['url'] ?? $newShare['element']['url'] ?? $newShare[0]['url'] ?? '';
                    if ($shareUrl) {
                        $downloadUrl = rtrim($shareUrl, '/') . '/download';
                        $existingSharesByPath[$fullPath] = $shareUrl;
                    }
                } catch (\Throwable $e) {
                    // In case of an error, downloadUrl remains empty
                }
            }

            $result[] = [
                'name' => $file,
                'downloadUrl' => $downloadUrl,
                'size' => $sizeInBytes,
                'sizeInMb' => $sizeInMb,
            ];
        }

        return $result;
    }
}
