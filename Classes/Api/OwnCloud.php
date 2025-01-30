<?php
namespace RKW\RkwCompetition\Api;

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

use Madj2k\CoreExtended\Utility\GeneralUtility;
use RKW\RkwCompetition\Api\OwnCloud\FolderApi;
use RKW\RkwCompetition\Api\OwnCloud\GroupsApi;
use RKW\RkwCompetition\Api\OwnCloud\UsersApi;


/**
 * Class OwnCloud
 *
 * A service class to bundle the several OwnCloud API types into one ("users", "groups", "apps")
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_Competition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class OwnCloud implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @return FolderApi
     */
    public function getFolderApi (): FolderApi
    {
        return GeneralUtility::makeInstance(FolderApi::class);
    }


    /**
     * @return GroupsApi
     */
    public function getGroupsApi (): GroupsApi
    {
        return GeneralUtility::makeInstance(GroupsApi::class);
    }


    /**
     * @return UsersApi
     */
    public function getUsersApi (): UsersApi
    {
        return GeneralUtility::makeInstance(UsersApi::class);
    }


}
