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


/**
 * Class FolderApi
 *
 * Works with WebDav
 *
 * https://code.blogs.iiidefix.net/posts/webdav-with-curl/
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_Competition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FolderApi extends AbstractApi
{

    const API_PATH = 'remote.php/webdav/';

    /**
     * addFolder
     *
     * @param array $folderPath Folders as sequential array
     * @return array
     */
    public function addFolder(array $folderPath): array
    {
        $this->apiMethod = 'MKCOL';

        return $this->doApiRequest(self::API_PATH . implode('/', $folderPath));
    }

    /**
     * removeFolder
     *
     * @param array $existingFolderPath Folders as sequential array
     * @return array
     */
    public function removeFolder(array $existingFolderPath): array
    {
        $this->apiMethod = self::METHOD_DELETE;

        return $this->doApiRequest(self::API_PATH . implode('/', $existingFolderPath));
    }


}