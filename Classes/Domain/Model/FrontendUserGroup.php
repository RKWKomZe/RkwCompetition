<?php
namespace RKW\RkwCompetition\Domain\Model;

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

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class FrontendUserGroup
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package Rkw_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class FrontendUserGroup extends \Madj2k\FeRegister\Domain\Model\FrontendUserGroup
{

    /**
     * @todo: Ask SK for typecast issue (RegisterController->"create" on var_dump)
     * @var int
     */
    protected int $crdate = 0;


    /**
     * @todo: Ask SK for typecast issue (RegisterController->"create" on var_dump)
     * @var int
     */
    protected int $tstamp = 0;



}
