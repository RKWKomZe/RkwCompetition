<?php
namespace RKW\RkwCompetition\Validation\Validator;

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

use Madj2k\CoreExtended\Utility\GeneralUtility as Common;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class REgisterValidator
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class RegisterValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{

    /**
     * TypoScript Settings
     *
     * @var array
     */
    protected $settings = null;

    /**
     * validation
     *
     * @var \RKW\RkwCompetition\Domain\Model\Register $newRegister
     * @return bool
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function isValid($newRegister): bool
    {

        // @toDo: Check if email is valid \Madj2k\FeRegister\Utility\FrontendUserUtility::isEmailValid



        // initialize typoscript settings
        $this->getSettings();

        // get mandatory fields
        $mandatoryFields = GeneralUtility::trimExplode(",", $this->settings['mandatoryFields']['register']);

        // add further fields if "groupWork" is selected
        if ($newRegister->getIsGroupWork()) {
            $mandatoryFields = GeneralUtility::trimExplode(",", $this->settings['mandatoryFields']['registerGroupWork']);
        }

        $isValid = true;

        // check if register period is already expired
        if ($newRegister->getCompetition()->getRegisterEnd()->getTimestamp() < time()) {
            $this->result->addError(
                new \TYPO3\CMS\Extbase\Error\Error(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'tx_rkwcompetition_validator.expired',
                        'rkw_competition',
                        [date('d.m.Y', $newRegister->getCompetition()->getRegisterEnd()->getTimestamp())]
                    ), 1736779680
                )
            );
            $isValid = false;
        }


        // 1. Check mandatory fields main person
        if ($mandatoryFields) {

            foreach ($mandatoryFields as $field) {

                $getter = 'get' . ucfirst($field);
                if (method_exists($newRegister, $getter)) {

                    if ( !trim($newRegister->$getter()) ) {

                        $propertyName = LocalizationUtility::translate(
                            'tx_rkwcompetition_validator.' . lcfirst($field),
                            'rkw_competition'
                        );

                        $this->result->forProperty(lcfirst($field))->addError(
                            new Error(
                                LocalizationUtility::translate(
                                    'tx_rkwcompetition_validator.not_filled',
                                    'rkw_competition',
                                    [$propertyName]
                                ), 1731910556
                            )
                        );
                        $isValid = false;
                    }
                }
            }
        }

        return $isValid;
    }


    /**
     * Returns TYPO3 settings
     *
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function getSettings(): array
    {

        if (!$this->settings) {
            $this->settings = Common::getTypoScriptConfiguration('Rkwcompetition');
        }

        if (!$this->settings) {
            return [];
        }
        //===

        return $this->settings;
        //===
    }

}

