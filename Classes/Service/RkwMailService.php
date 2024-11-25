<?php

namespace RKW\RkwCompetition\Service;

use Madj2k\CoreExtended\Utility\GeneralUtility as Common;
use Madj2k\FeRegister\Domain\Model\BackendUser;
use Madj2k\FeRegister\Domain\Model\FrontendUser;
use Madj2k\Postmaster\Exception;
use Madj2k\Postmaster\Mail\MailMessage;
use RKW\RkwCompetition\Domain\Model\Register;
use SJBR\StaticInfoTables\Domain\Model\Language;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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
 * RkwMailService
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class RkwMailService implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * Handles opt-in event
     *
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser
     * @param \Madj2k\FeRegister\Domain\Model\OptIn $optIn
     * @param mixed $signalInformation
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function optInRequest(
        \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser,
        \Madj2k\FeRegister\Domain\Model\OptIn $optIn,
        $signalInformation
    ) :void
    {
        // get settings
        $settings = $this->getSettings(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $settingsDefault = $this->getSettings();

        if ($settings['view']['templateRootPaths']) {

            /** @var \Madj2k\Postmaster\Mail\MailMessage $mailService */
            $mailService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(MailMessage::class);

            // send new user an email with token
            $mailService->setTo($frontendUser, [
                'marker' => [
                    'frontendUser'      => $frontendUser,
                    'optIn'             => $optIn,
                    'pageUid'           => intval($GLOBALS['TSFE']->id),
                    'competitionPid'    => intval($settingsDefault['competitionPid']),
                    'loginPid'          => intval($settingsDefault['loginPid']),
                    'showPid'           => intval($settingsDefault['showPid'])
                ],
            ]);


//            // set reply address
//            if (
//                (ExtensionManagementUtility::isLoaded('rkw_authors'))
//                && ($optIn->getData()->getEvent())
//            ){
//                if (count($optIn->getData()->getEvent()->getInternalContact()) > 0) {
//
//                    /** @var \RKW\RkwCompetition\Domain\Model\Authors $contact */
//                    foreach ($optIn->getData()->getEvent()->getInternalContact() as $contact) {
//
//                        if ($contact->getEmail()) {
//                            $mailService->getQueueMail()->setReplyToAddress($contact->getEmail());
//                            break;
//                        }
//                    }
//                }
//            }

            $mailService->getQueueMail()->setSubject(
                LocalizationUtility::translate(
                    'rkwMailService.optInRegisterUser.subject',
                    'rkw_competition',
                    null,
                    $frontendUser->getTxFeregisterLanguageKey()
                )
            );

            $mailService->getQueueMail()->addTemplatePaths($settings['view']['templateRootPaths']);
            $mailService->getQueueMail()->addPartialPaths($settings['view']['partialRootPaths']);

            $mailService->getQueueMail()->setPlaintextTemplate('Email/OptInRegisterUser');
            $mailService->getQueueMail()->setHtmlTemplate('Email/OptInRegisterUser');

            $mailService->send();
        }
    }


    /**
     * Handles confirm mail for user
     * Works with FeRegister-FrontendUser -> this is correct! (data comes from TxFeRegister)
     *
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function confirmRegistrationUser(
        \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser,
        \RKW\RkwCompetition\Domain\Model\Register $register
    ) :void
    {
        // send confirmation
        $this->userMail($frontendUser, $register, 'confirmation', true);
    }


    /**
     * Send Mail to user for contribution documents
     *
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function uploadDocumentsUser(
        \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser,
        \RKW\RkwCompetition\Domain\Model\Register $register
    ) :void
    {
        // send confirmation
        $this->userMail($frontendUser, $register, 'upload', true);
    }


    /**
     * Handles confirm mail for admin
     *
     * @param BackendUser|array $backendUser
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function confirmRegistrationAdmin(
        $backendUser,
        \RKW\RkwCompetition\Domain\Model\Register $register
    ) :void
    {
        $this->adminMail($backendUser, $register, 'confirmation');
    }


    /**
     * Handles confirm mail for user
     * Works with FeRegister-FrontendUser -> this is correct! (data comes from TxFeRegister)
     *
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
//    public function confirmRegisterUser(
//        \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser,
//        \RKW\RkwCompetition\Domain\Model\Register $register
//    ) :void
//    {
//        // send confirmation
//        $this->userMail($frontendUser, $register, 'confirmation');
//    }


    /**
     * Handles confirm mail for admin
     *
     * @param BackendUser|array $backendUser
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
//    public function confirmRegisterAdmin(
//        $backendUser,
//        \RKW\RkwCompetition\Domain\Model\Register $register
//    ) :void
//    {
//        $this->adminMail($backendUser, $register, 'confirmation');
//    }

    /**
     * Handles update mail for user
     *
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function updateRegisterUser(
        \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser,
        \RKW\RkwCompetition\Domain\Model\Register $register
    ) :void
    {
        $this->userMail($frontendUser, $register, 'update');
    }


    /**
     * Handles update mail for admin
     *
     * @param BackendUser|array $backendUser
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function updateRegisterAdmin(
        $backendUser,
        \RKW\RkwCompetition\Domain\Model\Register $register
    ) :void
    {
        $this->adminMail($backendUser, $register, 'update');
    }


    /**
     * Handles delete mail for user
     *
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function deleteRegisterUser(
        \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser,
        \RKW\RkwCompetition\Domain\Model\Register $register
    ) :void
    {
        $this->userMail($frontendUser, $register, 'delete');
    }


    /**
     * Handles delete mail for admin
     *
     * @param BackendUser|array $backendUser
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function deleteRegisterAdmin(
        $backendUser,
        \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser,
        \RKW\RkwCompetition\Domain\Model\Register $register
    ) :void
    {
        $this->adminMail($backendUser, $register, 'delete', $frontendUser);
    }


    /**
     * Sends an E-Mail to a Frontend-User
     *
     * @param FrontendUser $frontendUser
     * @param Register $register
     * @param string $action
     * @return void
     * @throws Exception
     * @throws IllegalObjectTypeException
     * @throws InvalidConfigurationTypeException
     * @throws UnknownObjectException
     */
    protected function userMail(
        \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser,
        \RKW\RkwCompetition\Domain\Model\Register    $register,
        string $action = ''
    ) :void
    {
        // get settings
        $settings = $this->getSettings(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $settingsDefault = $this->getSettings();
        $showPid = intval($settingsDefault['showPid']);

        // if plugin is "standaloneregister" (and not "pi1") set flag to true. Needed in mail template
        $request = GeneralUtility::_GP('tx_RkwCompetition_standaloneregister');
        $isStandaloneRegisterPlugin = (bool)$request;
        if ($isStandaloneRegisterPlugin) {
            // if standalone Register form plugin: Override showPid!
            $showPid = intval($GLOBALS['TSFE']->id);
        }

        if ($settings['view']['templateRootPaths']) {

            /** @var \Madj2k\Postmaster\Mail\MailMessage $mailService */
            $mailService = GeneralUtility::makeInstance(MailMessage::class);

            // send new user an email with token
            $mailService->setTo($frontendUser, [
                'marker' => [
                    'register'          => $register,
                    'frontendUser'      => $frontendUser,
                    'pageUid'           => intval($GLOBALS['TSFE']->id),
                    'competitionPid'    => intval($settingsDefault['competitionPid']),
                    'loginPid'          => intval($settingsDefault['loginPid']),
                    'showPid'           => $showPid,
                    'uniqueKey'         => uniqid(),
                    'currentTime'       => time(),
                ],
            ]);

//            // set reply address
//            if (
//                (ExtensionManagementUtility::isLoaded('rkw_authors'))
//                && ($register->getEvent())
//            ){
//                if (count($register->getEvent()->getInternalContact()) > 0) {
//
//                    /** @var \RKW\RkwCompetition\Domain\Model\Authors $contact */
//                    foreach ($register->getEvent()->getInternalContact() as $contact) {
//
//                        if ($contact->getEmail()) {
//                            $mailService->getQueueMail()->setReplyToAddress($contact->getEmail());
//                            break;
//                        }
//                    }
//                }
//            }

            $mailService->getQueueMail()->setSubject(
                LocalizationUtility::translate(
                    'rkwMailService.' . strtolower($action) . 'RegisterUser.subject',
                    'rkw_competition',
                    null,
                    $frontendUser->getTxFeregisterLanguageKey()
                )
            );

            $mailService->getQueueMail()->addTemplatePaths($settings['view']['templateRootPaths']);
            $mailService->getQueueMail()->addPartialPaths($settings['view']['partialRootPaths']);

            $mailService->getQueueMail()->setPlaintextTemplate('Email/' . ucfirst(strtolower($action)) . 'RegisterUser');
            $mailService->getQueueMail()->setHtmlTemplate('Email/' . ucfirst(strtolower($action)) . 'RegisterUser');

            $mailService->send();
        }
    }


    /**
     * Sends an E-Mail to an Admin
     *
     * @param BackendUser|array $backendUser
     * @param Register $register
     * @param string $action
     * @param FrontendUser|null $frontendUser
     * @throws Exception
     * @throws InvalidConfigurationTypeException
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @return void
     */
    protected function adminMail(
        $backendUser,
        \RKW\RkwCompetition\Domain\Model\Register $register,
        string $action = '',
        \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser = null
    ) :void
    {
        // get settings
        $settings = $this->getSettings(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $settingsDefault = $this->getSettings();
        $showPid = intval($settingsDefault['showPid']);

        $recipients = [];
        if (is_array($backendUser)) {
            $recipients = $backendUser;
        } else {
            $recipients[] = $backendUser;
        }

        if ($settings['view']['templateRootPaths']) {

            /** @var \Madj2k\Postmaster\Mail\MailMessage $mailService */
            $mailService = GeneralUtility::makeInstance(MailMessage::class);

            foreach ($recipients as $recipient) {

                if (
                    (
                        ($recipient instanceof BackendUser)
                        || ($recipient instanceof EventContact)
                    )
                    && ($recipient->getEmail())
                ) {

                    $language = $recipient->getLang();
                    if ($language instanceof Language) {
                        $language = $language->getTypo3Code();
                    }

                    $name = '';
                    if ($recipient instanceof BackendUser) {
                        $name = $recipient->getRealName();
                    }
                    if ($recipient instanceof EventContact) {
                        $name = $recipient->getFirstName() . ' ' . $recipient->getLastName();
                    }

                    // send new user an email with token
                    $mailService->setTo($recipient, [
                        'marker'  => [
                            'register'          => $register,
                            'admin'             => $recipient,
                            'frontendUser'      => $frontendUser,
                            'pageUid'           => intval($GLOBALS['TSFE']->id),
                            'competitionPid'    => intval($settingsDefault['competitionPid']),
                            'loginPid'          => intval($settingsDefault['loginPid']),
                            'showPid'           => $showPid,
                            'fullName'          => $name,
                            'language'          => $language,
                        ],
                        'subject' => LocalizationUtility::translate(
                            'rkwMailService.' . strtolower($action) . 'RegisterAdmin.subject',
                            'rkw_competition',
                            null,
                            $recipient->getLang()
                        ),
                    ]);
                }
            }

            if (
                ($register->getFrontendUser())
                && ($register->getFrontendUser()->getEmail())
            ) {
                $mailService->getQueueMail()->setReplyAddress($register->getFrontendUser()->getEmail());
            }

            $mailService->getQueueMail()->setSubject(
                LocalizationUtility::translate(
                    'rkwMailService.' . strtolower($action) . 'RegisterAdmin.subject',
                    'rkw_competition',
                    null,
                    'de'
                )
            );

            $mailService->getQueueMail()->addTemplatePaths($settings['view']['templateRootPaths']);
            $mailService->getQueueMail()->addPartialPaths($settings['view']['partialRootPaths']);

            $mailService->getQueueMail()->setPlaintextTemplate('Email/' . ucfirst(strtolower($action)) . 'RegisterAdmin');
            $mailService->getQueueMail()->setHtmlTemplate('Email/' . ucfirst(strtolower($action)) . 'RegisterAdmin');

            if (count($mailService->getTo())) {
                $mailService->send();
            }
        }
    }


    /**
     * Returns TYPO3 settings
     *
     * @param string $which Which type of settings will be loaded
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function getSettings(string $which = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS): array
    {
        return Common::getTypoScriptConfiguration('RkwCompetition', $which);
    }
}
