<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Controller;


use Madj2k\FeRegister\DataProtection\ConsentHandler;
use Madj2k\FeRegister\Domain\Model\FrontendUser;
use Madj2k\FeRegister\Registration\FrontendUserRegistration;
use Madj2k\FeRegister\Utility\FrontendUserUtility;
use RKW\RkwCompetition\Domain\Model\Competition;
use Madj2k\CoreExtended\Domain\Model\FileReference;
use RKW\RkwCompetition\Domain\Model\Register;
use RKW\RkwCompetition\Persistence\FileHandler;
use RKW\RkwCompetition\Service\RkwMailService;
use RKW\RkwCompetition\Utility\CompetitionUtility;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * This file is part of the "RKW Competition" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 Maximilian Fäßler <maximilian@faesslerweb.de>, RKW Kompetenzzentrum
 */

/**
 * RegisterController
 */
class RegisterController extends \RKW\RkwCompetition\Controller\AbstractController
{
    /**
     * Signal name for use in ext_localconf.php
     *
     * @const string
     */
    const SIGNAL_AFTER_REGISTER_CREATED_ADMIN = 'afterRegisterCreatedAdmin';


    /**
     * Signal name for use in ext_localconf.php
     *
     * @const string
     */
    const SIGNAL_AFTER_REGISTER_CREATED_USER = 'afterRegisterCreatedUser';


    /**
     * Signal name for use in ext_localconf.php
     *
     * @const string
     */
    const SIGNAL_AFTER_REGISTER_UPDATE_ADMIN = 'afterUpdateRegisterAdmin';


    /**
     * Signal name for use in ext_localconf.php
     *
     * @const string
     */
    const SIGNAL_AFTER_REGISTER_UPDATE_USER = 'afterUpdateRegisterUser';


    /**
     * Signal name for use in ext_localconf.php
     *
     * @const string
     */
    const SIGNAL_AFTER_REGISTER_DELETE_ADMIN = 'afterDeleteRegisterAdmin';


    /**
     * Signal name for use in ext_localconf.php
     *
     * @const string
     */
    const SIGNAL_AFTER_REGISTER_DELETE_USER = 'afterDeleteRegisterUser';


    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $registers = $this->registerRepository->findAll();
        $this->view->assign('registers', $registers);
    }



    /**
     * action new
     *
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @return string|object|null|void
     */
    public function newAction(\RKW\RkwCompetition\Domain\Model\Competition $competition)
    {

        // @toDo: Gets $competition via argument

        // @toDo: We could also have a flexForm with given $competition ID

        //$this->view->assign('sectorList', $this->sectorRepository->findAll());
        $this->view->assign('competition', $competition);

    }


    /**
     * action create
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $newRegister
     * @TYPO3\CMS\Extbase\Annotation\Validate("RKW\RkwCompetition\Validation\Validator\RegisterValidator", param="newRegister")
     * @TYPO3\CMS\Extbase\Annotation\Validate("Madj2k\FeRegister\Validation\Consent\TermsValidator", param="newRegister")
     * @TYPO3\CMS\Extbase\Annotation\Validate("Madj2k\FeRegister\Validation\Consent\PrivacyValidator", param="newRegister")
     * @return void
     * @throws AspectNotFoundException
     * @throws StopActionException
     */
    public function createAction(\RKW\RkwCompetition\Domain\Model\Register $newRegister)
    {

        // -> create an additional field inside the RegisterModel for it
        $newRegister->setUniqueId(uniqid('feuser', false));


        // @toDo: Check register end time of competition


        $newRegister->setPrivacy(time());
        $newRegister->setConditionsOfParticipation(time());

        if (
            ($this->getFrontendUser())
            && (FrontendUserUtility::isEmailValid($this->getFrontendUser()->getEmail()))
        ) {

            // @toDo: Try-catch registration

            $this->finalSaveRegistration($newRegister, $this->getFrontendUser());

            // add privacy info
            ConsentHandler::add(
                $this->request,
                $this->getFrontendUser(),
                $newRegister,
                'new competition register'
            );

            $this->addFlashMessage(
                LocalizationUtility::translate(
                    'registerController.message.registrationCreated',
                    'rkw_competition'
                )
            );

        } else {

            // register new user or simply send opt-in to existing user
            /** @var \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser */
            $frontendUser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(FrontendUser::class);
            // @toDo: Check salutation typecast after CD feedback to using of Title-Model
            $frontendUser->setTxFeregisterGender((int)$newRegister->getSalutation());
            $frontendUser->setFirstName($newRegister->getFirstName());
            $frontendUser->setLastName($newRegister->getLastName());
            $frontendUser->setCompany($newRegister->getInstitution());
            $frontendUser->setAddress($newRegister->getAddress());
            $frontendUser->setZip($newRegister->getZip());
            $frontendUser->setCity($newRegister->getCity());
            $frontendUser->setEmail($newRegister->getEmail());

            /** @var \Madj2k\FeRegister\Registration\FrontendUserRegistration $registration */
            $registration = $this->objectManager->get(FrontendUserRegistration::class);
            $registration->setFrontendUser($frontendUser)
                ->setData($newRegister)
                ->setDataParent($newRegister->getCompetition())
                ->setCategory('rkwCompetition')
                ->setRequest($this->request)
                ->startRegistration();

            $this->addFlashMessage(
                LocalizationUtility::translate('registerController.message.registrationCreatedEmail')
            );
        }


        $this->redirect(
            'show',
            'Competition',
            null,
            ['competition' => $newRegister->getCompetition()],
            $this->settings['competitionPid']
        );
    }



    /**
     * action edit
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("register")
     * @return void
     */
    public function editAction(\RKW\RkwCompetition\Domain\Model\Register $register)
    {

        // @toDo: Check for logged in user

        $this->view->assign('register', $register);
    }



    /**
     * action update
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     */
    public function updateAction(\RKW\RkwCompetition\Domain\Model\Register $register)
    {

        // @toDo: Check for logged in user

        $this->addFlashMessage(
            LocalizationUtility::translate('registerController.message.updated')
        );
        $this->registerRepository->update($register);
        $this->redirect('list', 'Participant');
    }



    /**
     * action deleteQuestion
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     */
    public function deleteQuestionAction(\RKW\RkwCompetition\Domain\Model\Register $register)
    {

        // @toDo: Check for logged in user

        $this->view->assign('register', $register);
    }



    /**
     * action delete
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     */
    public function deleteAction(\RKW\RkwCompetition\Domain\Model\Register $register)
    {
        // @toDo: Check for logged in user

        $this->addFlashMessage(
            LocalizationUtility::translate('registerController.message.updated')
        );
        $this->registerRepository->remove($register);

        // Remove complete fileFolder

        /** @var \RKW\RkwCompetition\Persistence\FileHandler $fileHandler */
        $fileHandler = GeneralUtility::makeInstance(FileHandler::class);
        $folderIdentifier = $fileHandler->createFolderIdentifierByFolderName($register->getUniqueId());
        $fileHandler->removeAllFilesOfFolderFromHdd($folderIdentifier);
        $fileHandler->removeFolderFromHddByIdentifier($folderIdentifier);


        // @toDo: Remove API stuff


        $emailService = \Madj2k\CoreExtended\Utility\GeneralUtility::makeInstance(RkwMailService::class);
        // Email an Teilnehmenden: "Teilnahme zurückgezogen"
        $emailService->deleteRegisterUser($register->getFrontendUser(), $register);
        // Email an Admins (siehe #4198):  "Teilnahme zurückgezogen"
        // 5. send information mail to be-users
        $adminMails = [];
        if ($backendUserList = $register->getCompetition()->getAdminMember()) {
            /** @var \Madj2k\FeRegister\Domain\Model\BackendUser $backendUser */
            foreach ($backendUserList as $backendUser) {
                if ($backendUser->getEmail()) {
                    $adminMails[] = $backendUser;
                }
            }
            $emailService->deleteRegisterAdmin($adminMails, $register);
        }




        // Folgende Punkte: (Ggf über Cronjob mit einbeziehen, anstatt einzelne Lösch-Queries zu starten?)

        // @toDo: Löschen des Beitragsdatensatzens inklusive aller hochgeladener Dateien

        // @toDo: Löschen des Cloud-Ordners (siehe #4200) --------

        $this->redirect('list', 'Participant');
    }


    /**
     * action submitQuestion
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     */
    public function submitQuestionAction(\RKW\RkwCompetition\Domain\Model\Register $register)
    {
        // @toDo: Check for logged in user

        $this->view->assign('register', $register);
    }


    /**
     * action submit
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @param int $submitConfirm
     * @return void
     */
    public function submitAction(
        \RKW\RkwCompetition\Domain\Model\Register $register,
        int $submitConfirm = 0
    )
    {
        // @toDo: Check for logged in user

        if (!$submitConfirm) {

            $this->addFlashMessage(
                LocalizationUtility::translate('registerController.message.submitIncomplete')
            );
            $this->redirect(
                'submitQuestion',
                'Register',
                null,
                ['register' => $register]
            );
        }


        $this->addFlashMessage(
            LocalizationUtility::translate('registerController.message.submitSuccess')
        );

        $register->setUserSubmittedAt(time());

        $this->registerRepository->update($register);



        $emailService = \Madj2k\CoreExtended\Utility\GeneralUtility::makeInstance(RkwMailService::class);


        // @toDo: Email an Teilnehmenden: "Unterlagen eingereicht"
        $emailService->submitRegisterUser($register->getFrontendUser(), $register);
        // @toDo: Email an Admins (siehe #4198):  "Unterlagen eingereicht"
        // 5. send information mail to be-users
        $adminMails = [];
        if ($backendUserList = $register->getCompetition()->getAdminMember()) {
            /** @var \Madj2k\FeRegister\Domain\Model\BackendUser $backendUser */
            foreach ($backendUserList as $backendUser) {
                if ($backendUser->getEmail()) {
                    $adminMails[] = $backendUser;
                }
            }
            $emailService->submitRegisterAdmin($adminMails, $register);
        }

        $this->redirect('list', 'Participant');
    }



    /**
     * action optIn
     * Comment by Maximilian Fäßler: We get tricky validation issues here (https://rkwticket.rkw.de/issues/2803)
     * -> So we ignore the validation itself and checking with internal alias "instanceof" for trustful data
     * -> Benefit: We can set more helpful error messages for frontend users
     *
     * @param Competition $competition
     * @param string $tokenUser
     * @param string $token
     * @return void
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("competition")
     * @throws StopActionException
     */
    public function optInAction(Competition $competition, string $tokenUser, string $token): void
    {

        // General error:
        if ($competition->_isDirty()) {
            $this->addFlashMessage(
                LocalizationUtility::translate('registerController.error.somethingWentWrong', 'rkw_competition'),
                '',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
            $this->redirect('show', 'Competition', null, ['competition' => $competition], (int)$this->settings['competitionPid']);
        }



    //    DebuggerUtility::var_dump("test"); exit;

        /** @var \Madj2k\FeRegister\Registration\FrontendUserRegistration $registration */
        $registration = $this->objectManager->get(FrontendUserRegistration::class);
        $result = $registration->setFrontendUserToken($tokenUser)
            ->setCategory('rkwCompetition')
            ->setRequest($this->request)
            ->validateOptIn($token);

        if (
            ($result >= 200 && $result < 300)
            && ($optIn = $registration->getOptInPersisted())
            && ($newRegister = $optIn->getData())
            && ($newRegister instanceof Register)
            && ($frontendUser = $registration->getFrontendUserPersisted())
        ) {

            // 1. we need to re-fetch the competition here, since the number of available seats or the dates may have been changed
            if ($result == 200) {

                /** @var Competition $competition */
                $competition = $this->competitionRepository->findByIdentifier($competition);
                $newRegister->setCompetition($competition);

                // 2. Check for existing registration based on email.
                $registerQueryResult = $this->registerRepository->findByCompetitionAndFrontendUser($competition, $frontendUser);
                if (count($registerQueryResult)) {

                    $this->addFlashMessage(
                        LocalizationUtility::translate('registerController.error.exists', 'rkw_competition'),
                        '',
                        \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
                    );

                    $uri = $this->uriBuilder->reset()
                        ->setTargetPageUid($this->settings['loginPid'])
                        ->build();

                    $this->addFlashMessage(
                        LocalizationUtility::translate(
                            'registerController.hint.registrations',
                            'rkw_competition',
                            [
                                0 => "<a href='" . $uri . "'>",
                                1 => "</a>",
                            ]
                        )
                    );

                    $this->redirect('show', 'Competition', null, ['competition' => $competition], (int)$this->settings['competitionPid']);
                }

                // 3. Check if registration-time is over since the user may have been waiting too long
                if (CompetitionUtility::hasRegTimeEnded($competition)) {

                    $this->addFlashMessage(
                        LocalizationUtility::translate('registerController.error.registrationTime', 'rkw_competition'),
                        '',
                        \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
                    );

                    $this->redirect('show', 'Competition', null, ['competition' => $competition], (int)$this->settings['competitionPid']);
                }




                // 4. Create registration!
                $this->finalSaveRegistration($newRegister, $frontendUser, $optIn);



                $this->addFlashMessage(
                    LocalizationUtility::translate(
                        'registerController.message.registrationCreated', 'rkw_competition'
                    )
                );

                $this->redirect('show', 'Competition', null, ['competition' => $competition], (int)$this->settings['competitionPid']);
            }


        } elseif ($result >= 300 && $result < 400) {

            $this->addFlashMessage(
                LocalizationUtility::translate(
                    'registerController.message.registrationCanceled', 'rkw_competition'
                )
            );

            $this->redirect('show', 'Competition', null, ['competition' => $competition], (int)$this->settings['competitionPid']);
        }


        $this->addFlashMessage(
            LocalizationUtility::translate(
                'registerController.error.registrationError', 'rkw_competition'
            ),
            '',
            \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
        );

        $this->redirect('show', 'Competition', null, ['competition' => $competition], (int)$this->settings['competitionPid']);

    }


    /**
     * finalSaveOrder
     * Adds the order finally to database and sends information mails to user and admin
     * This function is used by "optInAction" and "create"-function
     * Added by Maximilian Fäßler | FäßlerWeb
     *
     * @param Register $newRegister
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser
     * @param \Madj2k\FeRegister\Domain\Model\OptIn $optIn
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    protected function finalSaveRegistration(
        Register $newRegister,
        \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser,
        \Madj2k\FeRegister\Domain\Model\OptIn $optIn = null
    ): void
    {
        // persist new registration
        $newRegister->setFrontendUser($frontendUser);
        $this->registerRepository->add($newRegister);

        // add userGroup
        $frontendUser->addUsergroup($newRegister->getCompetition()->getGroupForUser());
        $this->frontendUserRepository->update($frontendUser);

        $this->persistenceManager->persistAll();

        // 4. send final confirmation mail to user
        $this->signalSlotDispatcher->dispatch(__CLASS__, self::SIGNAL_AFTER_REGISTER_CREATED_USER, [$frontendUser, $newRegister]);

        // 5. send information mail to be-users
        $adminMails = [];
        if ($backendUserList = $newRegister->getCompetition()->getAdminMember()) {
            /** @var \Madj2k\FeRegister\Domain\Model\BackendUser $backendUser */
            foreach ($backendUserList as $backendUser) {
                if ($backendUser->getEmail()) {
                    $adminMails[] = $backendUser;
                }
            }
            $this->signalSlotDispatcher->dispatch(__CLASS__, self::SIGNAL_AFTER_REGISTER_CREATED_ADMIN, [$adminMails, $newRegister]);
        }
    }


}
