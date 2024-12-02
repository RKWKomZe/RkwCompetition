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

    /**
     * Returns the status of a Register-Record
     * 100 = new
     * 200 = refused
     * 500 = complete
     *
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return int
     */
    public static function registerStatus(Register $register) :int
    {
        if ($register->getAdminApproved()) {
            // approved (complete)
            return 500;
        }

        if ($register->getAdminRefused()) {
            // refused
            return 200;
        }

        // new
        return 100;
    }



}
