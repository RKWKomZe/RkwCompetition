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


    /**
     * hasContent
     *
     * Service function which is using PROPFIND. Checks if the folder has content
     *
     * @param array $folderPath Folders as sequential array.
     * @return bool
     */
    public function hasContent(array $folderPath): bool
    {
        $this->apiMethod = self::METHOD_PROPFIND;
        $path = self::API_PATH . implode('/', $folderPath);
        $result = $this->doApiRequest($path);

        if ((int)key($result) === 207) {
            $xml = current($result);

            // Basic check for contents. OwnCloud PROPFIND returns the folder itself and its contents.
            // If more than one <d:response> or <D:response> (depending on server) is found, there's content.
            // Also, we can check for <d:getcontentlength> or <D:getcontentlength> that's not empty/0 for files.
            // But usually counting responses is enough if we know the first one is the folder itself.

            $dom = new \DOMDocument();
            // Use LIBXML_NOERROR to suppress warnings from invalid XML or unknown namespaces
            $dom->loadXML($xml, LIBXML_NOERROR);

            // OwnCloud often uses 'd' or 'D' as prefix, but the namespace is 'DAV:'
            $responses = $dom->getElementsByTagNameNS('DAV:', 'response');

            if ($responses->length > 1) {
                return true;
            }

            // Fallback: check without namespace if the above fails for some reason (though unlikely for DAV:)
            $responses = $dom->getElementsByTagName('response');
            if ($responses->length > 1) {
                return true;
            }

            // Another fallback: search for 'd:response' or 'D:response' in the raw XML if DOM failed
            if (preg_match_all('/<[a-zA-Z0-9]*:response/i', $xml) > 1) {
                return true;
            }
        }

        return false;
    }


}
