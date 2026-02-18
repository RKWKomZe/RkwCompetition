<?php
namespace RKW\RkwCompetition\Api\OwnCloud;

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

use Solarium\Component\Debug;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;


/**
 * Class WebDavApi
 *
 * - Files and Folder management
 * - Works with WebDav
 *
 * Further possible functions:
 * - Upload file
 * - Rename file
 *
 * https://code.blogs.iiidefix.net/posts/webdav-with-curl/
 * https://www.qed42.com/insights/using-curl-commands-with-webdav
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_Competition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class WebDavApi extends AbstractApi
{

    const API_PATH = 'remote.php/webdav/';

    public string $queryType = 'webdav';

    /**
     * addFolder
     *
     * Example:
     * - $ownCloud->getWebDavApi()->addFolder(['Documents', 'Something']);
     *
     * https://greenbytes.de/tech/webdav/rfc4918.html#METHOD_MKCOL
     *
     * @param array $folderPath Folders as sequential array
     * @return array
     */
    public function addFolder(array $folderPath): array
    {
        $this->apiMethod = self::METHOD_MKCOL;

        return $this->doApiRequest(self::API_PATH . implode('/', $folderPath));
    }


    /**
     * addFolderRecursive
     *
     * Service function which is using "addFolder". Can add not existing subfolder
     *
     * @param array $folderPath Folders as sequential array. Works recursive (to add not existing sub-folder)
     * @return array
     */
    public function addFolderRecursive(array $folderPath): array
    {
        $currentFolderPath = [];
        $result = [];

        foreach ($folderPath as $folder) {
            $currentFolderPath[] = $folder;

            // PROPFIND: exists?
            $this->apiMethod = self::METHOD_PROPFIND;
            $result = $this->doApiRequest(self::API_PATH . implode('/', $currentFolderPath));

            if ((int)key($result) === 404) {
                // MKCOL
                $this->addFolder($currentFolderPath);

                // PROPFIND again to return a "stable" exists-result
                $this->apiMethod = self::METHOD_PROPFIND;
                $result = $this->doApiRequest(self::API_PATH . implode('/', $currentFolderPath));
            }
        }

        return $result;
    }


    /**
     * removeFileOrFolder
     *
     * No recursive handling necessary here. A parent folder is always deleted together with it's childs
     *
     * Examples:
     * - Remove file: $ownCloud->getWebDavApi()->removeFileOrFolder(['Documents', 'Something', 'example.png']);
     * - Remove folder: $ownCloud->getWebDavApi()->removeFileOrFolder(['Documents', 'Something']);
     *
     * @param array $existingFolderPath Folders as sequential array
     * @return array
     */
    public function removeFileOrFolder(array $existingFolderPath): array
    {
        $this->apiMethod = self::METHOD_DELETE;

        return $this->doApiRequest(self::API_PATH . implode('/', $existingFolderPath));
    }


}
