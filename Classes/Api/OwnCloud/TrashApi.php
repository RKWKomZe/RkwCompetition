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
 * Class TrashApi
 *
 * https://doc.owncloud.com/server/next/developer_manual/webdav_api/trashbin.html
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_Competition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class TrashApi extends AbstractApi
{

    const API_PATH = 'dav/trash-bin/';


    /**
     * getGroupList
     *
     * 100 - successful
     *
     * https://doc.owncloud.com/server/next/developer_manual/core/apis/provisioning-api.html#get-groups-2
     *
     * @param string $search Optional argument to restrict the group search
     * @return array
     */
    public function getGroupList(string $search = ''): array
    {
        $this->apiMethod = self::METHOD_GET;

        $arguments = [
            'search' => $search
        ];

        return $this->doApiRequest(self::API_PATH, $arguments);
    }


    /**
     * addGroup
     *
     * 100 - successful
     * 101 - invalid input data
     * 102 - group already exists
     * 103 - failed to add the group
     *
     * https://doc.owncloud.com/server/next/developer_manual/core/apis/provisioning-api.html#add-user
     *
     * @param $groupName
     * @return array
     */
    public function addGroup($groupName): array
    {
        $arguments = [
            'groupid' => $groupName
        ];

        return $this->doApiRequest(self::API_PATH, $arguments);
    }


    /**
     * getGroup
     *
     * 100 - successful
     *
     * https://doc.owncloud.com/server/next/developer_manual/core/apis/provisioning-api.html#get-group
     *
     * @param string $groupName The ID of the group
     * @return array
     */
    public function getGroup(string $groupName): array
    {
        $this->apiMethod = self::METHOD_GET;

        return $this->doApiRequest(self::API_PATH . '/' . $groupName);
    }

}