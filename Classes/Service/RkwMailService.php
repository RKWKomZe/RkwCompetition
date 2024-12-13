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
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
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
     * Handles backendModule register approved user
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
    public function approvedRegisterUser(
        \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser,
        \RKW\RkwCompetition\Domain\Model\Register $register
    ) :void
    {
        $this->frontendUserMail($frontendUser, $register, 'approved');
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
    public function confirmRegisterUser(
        \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser,
        \RKW\RkwCompetition\Domain\Model\Register $register
    ) :void
    {
        // send confirmation
        $this->frontendUserMail($frontendUser, $register, 'confirmation');
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
        $this->frontendUserMail($frontendUser, $register, 'delete');
    }


    /**
     * Handles incomplete mail for user (unsubmitted registration)
     *
     * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $registerList
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function incompleteRegisterUser(\TYPO3\CMS\Extbase\Persistence\QueryResultInterface $registerList) :void
    {
        /** @var \RKW\RkwCompetition\Domain\Model\Register $register */
        foreach ($registerList as $register) {

            // @toDo: Check if frontendUser is set? (It's null if user was deleted)

            // send submitted
            $this->frontendUserMail($register->getFrontendUser(), $register, 'incomplete');

        }

    }



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

            $mailService->getQueueMail()->setPlaintextTemplate('Email/FrontendUser/OptIn');
            $mailService->getQueueMail()->setHtmlTemplate('Email/FrontendUser/OptIn');

            $mailService->send();
        }
    }


    /**
     * Handles backendModule register refused user
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
    public function refusedRegisterUser(
        \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser,
        \RKW\RkwCompetition\Domain\Model\Register $register
    ) :void
    {
        $this->frontendUserMail($frontendUser, $register, 'refused');
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
    public function submitRegisterUser(
        \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser,
        \RKW\RkwCompetition\Domain\Model\Register $register
    ) :void
    {
        // send submitted
        $this->frontendUserMail($frontendUser, $register, 'submit');
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
        $this->frontendUserMail($frontendUser, $register, 'upload');
    }


    /**
     * Handles jury notify mails
     *
     * @param array $frontendUserList FrontendUser inside an array
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function juryNotifyUser(
        array $frontendUserList,
        \RKW\RkwCompetition\Domain\Model\Competition $competition
    ) :void
    {
        foreach ($frontendUserList as $frontendUser) {
            $this->frontendUserMail($frontendUser, $competition, 'juryNotify');
        }
    }


    /**
     * Handles closing day mails for user without approved registrations (rejections)
     *
     * @param array $registerList Register records inside an array
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function closingDayIncompleteUser(
        array $registerList,
        \RKW\RkwCompetition\Domain\Model\Competition $competition
    ) :void
    {
        /** @var Register $register */
        foreach ($registerList as $register) {
            $this->frontendUserMail($register->getFrontendUser(), $competition, 'closingDayIncomplete');
        }
    }


    /**
     * Handles closing day mails for user with approved registrations (accepted)
     *
     * @param array $registerList Register records inside an array
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function closingDayApprovedUser(
        array $registerList,
        \RKW\RkwCompetition\Domain\Model\Competition $competition
    ) :void
    {
        /** @var Register $register */
        foreach ($registerList as $register) {
            $this->frontendUserMail($register->getFrontendUser(), $competition, 'closingDayApproved');
        }
    }


    /**
     * Sends an E-Mail to a Frontend-User
     *
     * @param FrontendUser $frontendUser
     * @param AbstractEntity $entity
     * @param string $action
     * @return void
     * @throws Exception
     * @throws IllegalObjectTypeException
     * @throws InvalidConfigurationTypeException
     * @throws UnknownObjectException
     */
    protected function frontendUserMail(
        \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser,
        \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $entity,
        string $action = ''
    ) :void
    {
        // get settings
        $settings = $this->getSettings(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $settingsDefault = $this->getSettings();
        $showPid = intval($settingsDefault['showPid']);

        if ($settings['view']['templateRootPaths']) {

            /** @var \Madj2k\Postmaster\Mail\MailMessage $mailService */
            $mailService = GeneralUtility::makeInstance(MailMessage::class);

            // get class name to set a comfortable marker variable for the email templates
            $classNameParts = GeneralUtility::trimExplode('\\', strtolower(get_class($entity)));
            $classNameUnqualified = end($classNameParts);

            // send new user an email with token
            $mailService->setTo($frontendUser, [
                'marker' => [
                    $classNameUnqualified   => $entity,
                    'frontendUser'          => $frontendUser,
                    'pageUid'               => intval($GLOBALS['TSFE']->id),
                    'competitionPid'        => intval($settingsDefault['competitionPid']),
                    'loginPid'              => intval($settingsDefault['loginPid']),
                    'showPid'               => $showPid,
                    'uniqueKey'             => uniqid(),
                    'currentTime'           => time(),
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
                    'rkwMailService.frontendUser.subject.' . $action,
                    'rkw_competition',
                    null,
                    $frontendUser->getTxFeregisterLanguageKey()
                )
            );

            $mailService->getQueueMail()->addTemplatePaths($settings['view']['templateRootPaths']);
            $mailService->getQueueMail()->addPartialPaths($settings['view']['partialRootPaths']);

            $mailService->getQueueMail()->setPlaintextTemplate('Email/FrontendUser/' . ucfirst($action));
            $mailService->getQueueMail()->setHtmlTemplate('Email/FrontendUser/' . ucfirst($action));

            $mailService->send();
        }
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
    public function confirmRegisterAdmin(
        $backendUser,
        \RKW\RkwCompetition\Domain\Model\Register $register
    ) :void
    {
        $this->backendUserMail($backendUser, $register, 'confirmation');
    }

    /**
     * Handles update mail for admin
     *
     * @todo Wird das genutzt?
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
    public function submitRegisterAdmin(
        $backendUser,
        \RKW\RkwCompetition\Domain\Model\Register $register
    ) :void
    {
        $this->backendUserMail($backendUser, $register, 'submit');
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
        \RKW\RkwCompetition\Domain\Model\Register $register
    ) :void
    {
        $this->backendUserMail($backendUser, $register, 'delete', $register->getFrontendUser());
    }


    /**
     * Handles delete mail for admin
     *
     * @param BackendUser|array $backendUser
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function removalOfCompetitionAdmin(
        $backendUser,
        \RKW\RkwCompetition\Domain\Model\Competition $competition
    ) :void
    {
        $this->backendUserMail($backendUser, $competition, 'removalReminder');
    }


    /**
     * Handles notify mails to admins on competitions closing day
     *
     * @param BackendUser|array $backendUser
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @return void
     * @throws \Madj2k\Postmaster\Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3Fluid\Fluid\View\Exception\InvalidTemplateResourceException
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function closingDayAdmin(
        $backendUser,
        \RKW\RkwCompetition\Domain\Model\Competition $competition
    ) :void
    {
        $this->backendUserMail($backendUser, $competition, 'closingDay');
    }


    /**
     * Sends an E-Mail to an Admin
     *
     * @param BackendUser|array $backendUser
     * @param AbstractEntity $entity
     * @param string $action
     * @param FrontendUser|null $frontendUser
     * @throws Exception
     * @throws InvalidConfigurationTypeException
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     * @return void
     */
    protected function backendUserMail(
        $backendUser,
        \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $entity,
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
        } else if ($backendUser instanceof ObjectStorage) {
            $recipients = $backendUser->toArray();
        } else {
            $recipients[] = $backendUser;
        }

        if ($settings['view']['templateRootPaths']) {

            /** @var \Madj2k\Postmaster\Mail\MailMessage $mailService */
            $mailService = GeneralUtility::makeInstance(MailMessage::class);
            foreach ($recipients as $recipient) {
                if (
                    $recipient instanceof BackendUser
                    && $recipient->getEmail()
                ) {

                    $language = $recipient->getLang();
                    if ($language instanceof Language) {
                        $language = $language->getTypo3Code();
                    }

                    // get class name to set a comfortable marker variable for the email templates
                    $classNameParts = GeneralUtility::trimExplode('\\', strtolower(get_class($entity)));
                    $classNameUnqualified = end($classNameParts);

                    // send new user an email with token
                    $mailService->setTo($recipient, [
                        'marker'  => [
                            // set variable "register" for Classname "Register; set "competition" for class "Competition"...
                            $classNameUnqualified           => $entity,
                            'admin'                         => $recipient,
                            'frontendUser'                  => $frontendUser,
                            'pageUid'                       => intval($GLOBALS['TSFE']->id),
                            'competitionPid'                => intval($settingsDefault['competitionPid']),
                            'loginPid'                      => intval($settingsDefault['loginPid']),
                            'showPid'                       => $showPid,
                            'fullName'                      => $recipient->getRealName(),
                            'language'                      => $language,
                        ],
                        'subject' => LocalizationUtility::translate(
                            'rkwMailService.backendUser.subject.' . strtolower($action),
                            'rkw_competition',
                            null,
                            $recipient->getLang()
                        ),
                    ]);
                }
            }


            if ($entity instanceof Register) {
                if (
                    ($entity->getFrontendUser())
                    && ($entity->getFrontendUser()->getEmail())
                ) {
                    $mailService->getQueueMail()->setReplyAddress($entity->getFrontendUser()->getEmail());
                }
            }

            $mailService->getQueueMail()->setSubject(
                LocalizationUtility::translate(
                    'rkwMailService.backendUser.subject.' . $action,
                    'rkw_competition',
                    null,
                    'de'
                )
            );

            $mailService->getQueueMail()->addTemplatePaths($settings['view']['templateRootPaths']);
            $mailService->getQueueMail()->addPartialPaths($settings['view']['partialRootPaths']);

            $mailService->getQueueMail()->setPlaintextTemplate('Email/BackendUser/' . $action);
            $mailService->getQueueMail()->setHtmlTemplate('Email/BackendUser/' . $action);

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
