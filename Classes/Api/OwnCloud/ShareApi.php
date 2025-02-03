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
 * Class ShareApi
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_Competition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ShareApi extends AbstractApi
{

    const API_PATH = 'ocs/v1.php/apps/files_sharing/api/v1/shares';


    /**
     * Share file or folder
     *
     * 100 Successful
     * 400 Unknown share type
     * 403 Public upload was disabled by the admin
     * 404 File or folder couldn’t be shared
     *
     * https://doc.owncloud.com/server/next/developer_manual/core/apis/ocs-share-api.html#create-a-new-share
     *
     * #############################################
     * Permission how to:
     * 1 = read (default for public link shares);
     * 2 = update;
     * 4 = create;
     * 8 = delete;
     * 16 = share;
     *
     * Common example combinations are:
     * 15 = read/write(update and create)/delete;
     * 31 = All permissions.
     * #############################################
     *
     * @param string $name A file or folder
     * @param array $path The path to the file or folder
     * @param int $shareType 0 = user; 1 = group; 3 = public link; 6 = federated cloud share
     * @param string $shareWith User or group ID
     * @param bool $publicUpload Whether to allow public upload to a public link shared folder
     * @param string $password The password to protect the public link share with
     * @param int $permissions The permissions to set on the share. 1 = read (default for public link shares); 2 = update; 4 = create; 8 = delete; 16 = share;
     * @param string $expireDate An expiry date for the user, group or public link share. This argument expects a date string in the following format 'YYYY-MM-DD'. The share expires at the end of the specified day.
     * @param array $attributes Contain a set of one or more permissions to set for a share
     * @return array
     */
    public function createShare(
        string $name,
        array $path,
        int $shareType,
        string $shareWith,
        bool $publicUpload = false,
        string $password = '',
        int $permissions = 0,
        string $expireDate = '',
        array $attributes = []
    ): array
    {
        $this->apiMethod = self::METHOD_POST;

        $arguments = [
            'name' => $name,
            'path' => implode('/', $path),
            'shareType' => $shareType,
            'shareWith' => $shareWith
        ];

        if ($publicUpload) {
            $arguments['publicUpload'] = $publicUpload;
        }
        if ($password) {
            $arguments['password'] = $password;
        }
        if ($permissions) {
            $arguments['permissions'] = $permissions;
        }
        if ($expireDate) {
            $arguments['expireDate'] = $expireDate;
        }
        if ($attributes) {
            $arguments['attributes'] = $attributes;
        }

        return $this->doApiRequest(self::API_PATH, $arguments);
    }


    /**
     * deleteShare
     *
     * 100 - successful
     * 101 - failure
     *
     * https://doc.owncloud.com/server/next/developer_manual/core/apis/ocs-share-api.html#delete-a-share
     *
     * @param int $shareId The share’s unique id
     * @return array
     */
    public function deleteShare(int $shareId): array
    {
        $this->apiMethod = self::METHOD_DELETE;
        return $this->doApiRequest(self::API_PATH . '/' . $shareId);
    }


}