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

use Brotkrueml\Schema\Model\Type\DanceEvent;
use Random\RandomException;
use RKW\RkwCompetition\Domain\Model\Competition;
use RKW\RkwCompetition\Domain\Model\Register;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * RegisterUtility
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class RegisterUtility
{
    const STATUS_NEW = 100;

    const STATUS_REFUSED = 200;

    const STATUS_SUBMITTED = 300;

    const STATUS_APPROVED = 500;


    /**
     * Returns the status of a register record
     * 100 = new
     * 200 = refused
     * 300 = submitted
     * 500 = approved
     *
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return int
     */
    public static function registerStatus(Register $register): int
    {
        if ($register->getAdminApprovedAt()) {
            // approved (500)
            return self::STATUS_APPROVED;
        }

        if ($register->getAdminRefusedAt()) {
            // refused (200)
            return self::STATUS_REFUSED;
        }

        if ($register->getUserSubmittedAt()) {
            // submitted (300)
            return self::STATUS_SUBMITTED;
        }

        // new (100)
        return self::STATUS_NEW;
    }



    /**
     * @param int $length
     * @return void
     * @throws RandomException
     */
    public static function createToken(int $length = 10): string
    {
        return bin2hex(random_bytes($length));
    }


}
