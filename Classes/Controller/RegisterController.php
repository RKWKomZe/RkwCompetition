<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Controller;


use Madj2k\FeRegister\Domain\Model\FrontendUser;
use Madj2k\FeRegister\Registration\FrontendUserRegistration;
use RKW\RkwCompetition\Domain\Model\Competition;
use RKW\RkwCompetition\Domain\Model\Register;
use RKW\RkwCompetition\Utility\CompetitionUtility;
use TYPO3\CMS\Core\Log\Logger;
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
class RegisterController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
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
     * competitionRepository
     *
     * @var \RKW\RkwCompetition\Domain\Repository\CompetitionRepository
     */
    protected $competitionRepository = null;

    /**
     * registerRepository
     *
     * @var \RKW\RkwCompetition\Domain\Repository\RegisterRepository
     */
    protected $registerRepository = null;

    /**
     * sectorRepository
     *
     * @var \RKW\RkwCompetition\Domain\Repository\SectorRepository
     */
    protected $sectorRepository = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?PersistenceManager $persistenceManager = null;

    /**
     * @param \RKW\RkwCompetition\Domain\Repository\RegisterRepository $registerRepository
     */
    public function injectRegisterRepository(\RKW\RkwCompetition\Domain\Repository\RegisterRepository $registerRepository)
    {
        $this->registerRepository = $registerRepository;
    }

    /**
     * @param \RKW\RkwCompetition\Domain\Repository\SectorRepository $sectorRepository
     */
    public function injectSectorRepository(\RKW\RkwCompetition\Domain\Repository\SectorRepository $sectorRepository)
    {
        $this->sectorRepository = $sectorRepository;
    }

    /**
     * @param \RKW\RkwCompetition\Domain\Repository\CompetitionRepository $competitionRepository
     */
    public function injectCompetitionRepository(\RKW\RkwCompetition\Domain\Repository\CompetitionRepository $CompetitionRepository)
    {
        $this->competitionRepository = $CompetitionRepository;
    }

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    public function injectPersistenceManager(PersistenceManager $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }



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
     * action show
     *
     * @deprecated Vermutlich nicht benötigt (nutze CompetitionController->show)
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     */
    public function showAction(\RKW\RkwCompetition\Domain\Model\Register $register)
    {
        $this->view->assign('register', $register);
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
     * @TYPO3\CMS\Extbase\Annotation\Validate("Madj2k\CoreExtended\Validation\CaptchaValidator", param="newRegister")
     * @return void
     */
    public function createAction(\RKW\RkwCompetition\Domain\Model\Register $newRegister)
    {

        // @toDo: Check register end time of competition


        $newRegister->setPrivacy(time());
        $newRegister->setConditionsOfParticipation(time());

        //$this->registerRepository->add($newRegister);

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
            LocalizationUtility::translate(
                'registerController.message.registrationCreatedEmail',
                'rkw_competition',
            ),
            '',
            \TYPO3\CMS\Core\Messaging\AbstractMessage::OK
        );



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
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->registerRepository->update($register);
        $this->redirect('list');
    }



    /**
     * action delete
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     */
    public function deleteAction(\RKW\RkwCompetition\Domain\Model\Register $register)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->registerRepository->remove($register);
        $this->redirect('list');
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

//        https://rkw-kompetenzzentrum.ddev.site:8443/themen/rg-bau/ueber-uns/wettbewerb-registrierung/
//?tx_rkwcompetition_register%5Baction%5D=optIn
//&tx_rkwcompetition_register%5Bcompetition%5D=1
//&tx_rkwcompetition_register%5Bcontroller%5D=Register
//&tx_rkwcompetition_register%5Btoken%5D=9fa7d3bf318cceb58257928d1a234c
//&tx_rkwcompetition_register%5BtokenUser%5D=7d5021a206b8598af263495a77ce28
//&cHash=2a659375db78b90837149dc09b85ad05#rkw-competition

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
        $newRegister->setFrontendUser($frontendUser);

        $this->registerRepository->add($newRegister);
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