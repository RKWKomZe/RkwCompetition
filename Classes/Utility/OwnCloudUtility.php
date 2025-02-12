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

use RKW\RkwCompetition\Domain\Model\Competition;
use RKW\RkwCompetition\Domain\Model\Register;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;


/**
 * OwnCloudUtility
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class OwnCloudUtility
{

    /**
     * Returns the password for the users ownCloud folder (FrontendUsers)
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return string
     */
    public static function getUserFolderSecret(Register $register): string
    {
        // simply "$register->getCrdate()->getTimestamp()" was used before, but lead to issues on using before it's created
        return md5($register->getFrontendUser()->getUid() . $register->getCompetition()->getCrdate()->getTimestamp());
    }


    /**
     * Returns the password for the competition ownCloud folder (jury; BeUsers)
     *
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @return string
     */
    public static function getCompetitionFolderSecret(Competition $competition): string
    {
        return md5($competition->getUid() . $competition->getCrdate()->getTimestamp());
    }
}
