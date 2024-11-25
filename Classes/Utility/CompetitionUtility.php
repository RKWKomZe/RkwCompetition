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
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * CompetitionUtility
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class CompetitionUtility
{

    /**
     * Returns true if reg time for event has ended
     *
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @return boolean
     */
    public static function hasRegTimeEnded(Competition $competition) :bool
    {
        // check if regEnd-time or start-time is in the past
        $date = $competition->getRegisterEnd() ?: $competition->getRegisterEnd();

        if ($date->getTimestamp() < time()) {
            return true;
        }

        return false;
    }


}
