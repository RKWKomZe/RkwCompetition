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
use RKW\RkwCompetition\Domain\Repository\RegisterRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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

    const STATUS_SUBMITTED = 300;

    const STATUS_RETURNED = 400;

    const STATUS_APPROVED = 500;

    const STATUS_REFUSED = 200;


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

        if ($register->getAdminReturnedAt()) {
            // returned (400)
            return self::STATUS_RETURNED;
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


    /**
     * Returns the registration number based on sector and sequence within competition
     * e.g. 1.01 for 1st registration in 1st sector, 3.04 for 4th registration in 3rd sector
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @param \RKW\RkwCompetition\Domain\Model\Competition|null $competition
     * @return string
     */
    public static function getRegistrationNumber(Register $register, ?Competition $competition = null): string
    {
        $competition = $competition ?: $register->getCompetition();
        if ($competition === null) {
            return '-';
        }

        $sector = $register->getSector();
        if ($sector === null) {
            return '-';
        }

        // Determine category index (1-based) from competition's sectors
        $categoryIndex = 0;
        $counter = 1;
        if ($competition->getSectors()) {
            foreach ($competition->getSectors() as $compSector) {
                if ($compSector->getUid() === $sector->getUid()) {
                    $categoryIndex = $counter;
                    break;
                }
                $counter++;
            }
        }

        if ($categoryIndex === 0) {
            return '-';
        }

        // Determine contribution index within this sector (1-based)
        $registers = $competition->getRegister();
        if ($registers === null || (is_countable($registers) && count($registers) === 0)) {
            $registerRepository = GeneralUtility::makeInstance(RegisterRepository::class);
            $registers = $registerRepository->findByCompetition($competition);
        }

        $sectorRegisterCount = 0;
        $contributionIndex = 0;
        if ($registers) {
            $registerArray = is_array($registers) ? $registers : iterator_to_array($registers);
            usort($registerArray, function (Register $a, Register $b) {
                return $a->getUid() <=> $b->getUid();
            });

            foreach ($registerArray as $compRegister) {
                if ($compRegister->getSector() && $compRegister->getSector()->getUid() === $sector->getUid()) {
                    $sectorRegisterCount++;
                    if ($compRegister->getUid() === $register->getUid()) {
                        $contributionIndex = $sectorRegisterCount;
                        break;
                    }
                }
            }
        }

        if ($contributionIndex === 0) {
            return '-';
        }

        return sprintf('%d.%02d', $categoryIndex, $contributionIndex);
    }


}
