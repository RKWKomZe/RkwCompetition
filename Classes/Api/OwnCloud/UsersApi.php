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
 * Class UsersApi
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_Competition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class UsersApi extends AbstractApi
{

    const API_PATH = 'ocs/v1.php/cloud/users';

    /**
     * addUser
     *
     * 100 - successful
     * 101 - invalid input data
     * 102 - username already exists
     * 103 - unknown error occurred whilst adding the user
     * 104 - group does not exist
     *
     * https://doc.owncloud.com/server/next/developer_manual/core/apis/provisioning-api.html#add-user
     *
     * @param string $username
     * @param string $password
     * @return array
     */
    public function addUser(string $username, string $password): array
    {
        $arguments = [
            'userid' => $username,
            'password' => $password
        ];

        return $this->doApiRequest(self::API_PATH, $arguments);
    }


    /**
     * getUserList
     *
     * 100 - successful
     *
     * https://doc.owncloud.com/server/next/developer_manual/core/apis/provisioning-api.html#get-users
     *
     * @param string $search Optional argument to restrict the user search
     * @return array
     */
    public function getUserList(string $search = ''): array
    {
        $this->apiMethod = self::METHOD_GET;

        $arguments = [
            'search' => $search
        ];

        return $this->doApiRequest(self::API_PATH, $arguments);
    }


    /**
     * getUser
     *
     * 100 - successful
     *
     * https://doc.owncloud.com/server/next/developer_manual/core/apis/provisioning-api.html#get-user
     *
     * @param string $username The ID of the user
     * @return array
     */
    public function getUser(string $username): array
    {
        $this->apiMethod = self::METHOD_GET;

        return $this->doApiRequest(self::API_PATH . '/' . $username);
    }


    /**
     * editUser
     *
     * 100 - successful
     * 101 - user not found
     * 102 - invalid input data
     *
     * https://doc.owncloud.com/server/next/developer_manual/core/apis/provisioning-api.html#edit-user
     *
     * The API can always change one field at once:
     *
     * -> Exactly one attribute can be set or modified at a time.
     * -> To set or modify multiple attributes then multiple calls must be made.
     *
     *
     * @param string $username The userId
     * @param string $targetField The property we want to change
     * @param string $newValue The new field value
     * @return array
     */
    public function editUser(string $username, string $targetField, string $newValue): array
    {
        $this->apiMethod = self::METHOD_PUT;

        $arguments = [
            'key' => $targetField,
            'value' => $newValue
        ];

        return $this->doApiRequest(self::API_PATH . '/' . $username, $arguments);
    }


    /**
     * enableUser
     *
     * 100 - successful
     * 101 - failure
     *
     * https://doc.owncloud.com/server/next/developer_manual/core/apis/provisioning-api.html#enable-user
     *
     * @param string $username The userId
     * @return array
     */
    public function enableUser(string $username): array
    {
        $this->apiMethod = self::METHOD_PUT;

        return $this->doApiRequest(self::API_PATH . '/' . $username . '/enable');
    }


    /**
     * disableUser
     *
     * 100 - successful
     * 101 - failure
     *
     * https://doc.owncloud.com/server/next/developer_manual/core/apis/provisioning-api.html#disable-user
     *
     * @param string $username The userId
     * @return array
     */
    public function disableUser(string $username): array
    {
        $this->apiMethod = self::METHOD_PUT;

        return $this->doApiRequest(self::API_PATH . '/' . $username . '/disable');
    }


    /**
     * deleteUser
     *
     * 100 - successful
     * 101 - failure
     *
     * https://doc.owncloud.com/server/next/developer_manual/core/apis/provisioning-api.html#delete-user
     *
     * @param $username
     * @return array
     */
    public function deleteUser($username): array
    {
        $this->apiMethod = self::METHOD_DELETE;
        return $this->doApiRequest(self::API_PATH . '/' . $username);
    }


    /**
     * Get groups of a user
     *
     * 100 - successful
     *
     * https://doc.owncloud.com/server/next/developer_manual/core/apis/provisioning-api.html#get-groups
     *
     * @param string $username The ID of the user
     * @return array
     */
    public function getGroups(string $username): array
    {
        $this->apiMethod = self::METHOD_GET;

        return $this->doApiRequest(self::API_PATH . '/' . $username . '/groups');
    }


    /**
     * Add user to a group
     *
     * 100 - successful
     * 101 - no group specified
     * 102 - group does not exist
     * 103 - user does not exist
     * 104 - insufficient privileges
     * 105 - failed to add user to group
     *
     * https://doc.owncloud.com/server/next/developer_manual/core/apis/provisioning-api.html#add-to-group
     *
     * @param string $username The ID of the user
     * @param string $groupName The name of the group
     * @return array
     */
    public function addToGroup(string $username, string $groupName): array
    {
        $this->apiMethod = self::METHOD_POST;

        $arguments = [
            'groupid' => $groupName,
        ];

        return $this->doApiRequest(self::API_PATH . '/' . $username . '/groups', $arguments);
    }


    /**
     * Remove user from a group
     *
     * 100 - successful
     * 101 - no group specified
     * 102 - group does not exist
     * 103 - user does not exist
     * 104 - insufficient privileges
     * 105 - failed to remove user from group
     *
     * https://doc.owncloud.com/server/next/developer_manual/core/apis/provisioning-api.html#remove-from-group
     *
     * @param string $username The ID of the user
     * @param string $groupName The name of the group
     * @return array
     */
    public function removeFromGroup(string $username, string $groupName): array
    {
        $this->apiMethod = self::METHOD_DELETE;

        $arguments = [
            'groupid' => $groupName,
        ];

        return $this->doApiRequest(self::API_PATH . '/' . $username . '/groups', $arguments);
    }


}