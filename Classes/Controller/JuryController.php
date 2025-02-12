<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Controller;


use Madj2k\CoreExtended\Utility\GeneralUtility;
use Madj2k\FeRegister\Domain\Model\GuestUser;
use Madj2k\FeRegister\Domain\Repository\GuestUserRepository;
use Madj2k\FeRegister\Registration\GuestUserRegistration;
use Madj2k\FeRegister\Service\GuestUserAuthenticationService;
use Madj2k\FeRegister\Utility\FrontendUserSessionUtility;
use RKW\RkwCompetition\Domain\Repository\JuryReferenceRepository;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * This file is part of the "RKW Competition" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 Maximilian Fäßler <maximilian@faesslerweb.de>, RKW Kompetenzzentrum
 */

/**
 *
 * JuryController
 */
class JuryController extends \RKW\RkwCompetition\Controller\AbstractController
{
    /**
     * juryReferenceRepository
     *
     * @var \RKW\RkwCompetition\Domain\Repository\JuryReferenceRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?JuryReferenceRepository $juryReferenceRepository = null;

    /**
     * guestUserRepository
     *
     * @var \Madj2k\FeRegister\Domain\Repository\GuestUserRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?GuestUserRepository $guestUserRepository = null;

    /**
     * @var \Madj2k\FeRegister\Registration\GuestUserRegistration
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected ?GuestUserRegistration $guestUserRegistration = null;

    /**
     * @param \RKW\RkwCompetition\Domain\Repository\JuryReferenceRepository $juryReferenceRepository
     */
    public function injectJuryReferenceRepository(\RKW\RkwCompetition\Domain\Repository\JuryReferenceRepository $juryReferenceRepository)
    {
        $this->juryReferenceRepository = $juryReferenceRepository;
    }

    /**
     * @param \Madj2k\FeRegister\Domain\Repository\GuestUserRepository $guestUserRepository
     */
    public function injectGuestUserRepository(\Madj2k\FeRegister\Domain\Repository\GuestUserRepository $guestUserRepository)
    {
        $this->guestUserRepository = $guestUserRepository;
    }

    /**
     * @var \Madj2k\FeRegister\Registration\GuestUserRegistration
     */
    public function injectGuestUserRegistration(\Madj2k\FeRegister\Registration\GuestUserRegistration $guestUserRegistration)
    {
        $this->guestUserRegistration = $guestUserRegistration;
    }



    /**
     * action list
     *
     * @deprecated Now we have anonymous logins for every competition
     *
     * @return void
     */
    public function listAction()
    {

        if (!$this->getFrontendUser()) {

            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                    'participantController.message.error.notPermitted',
                    'rkw_competition'
                ),
                '',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
        } else {

            $juryReferenceList = $this->juryReferenceRepository->findByFrontendUser($this->getFrontendUser());

            $this->view->assign('juryReferenceList', $juryReferenceList);
        }
    }


    /**
     * action show
     *
     * string $userToken
     * @throws AspectNotFoundException
     */
    public function showAction(string $userToken)
    {


        $guestUser = $this->guestUserRepository->findOneByUsernameIncludingDisabled($userToken);

        // @toDo: If token does not exists: Forward to login with error message


        $juryReference = $this->juryReferenceRepository->findByGuestUser($guestUser)->getFirst();


        // @toDo: generate and show login link (absolute link with token)
        $juryLoginLink = $this->uriBuilder->reset()
            ->setArguments(
                array('tx_rkwcompetition_jury' =>
                    array('userToken' => $guestUser->getUsername()),
                )
            )
            ->setCreateAbsoluteUri(true)
            ->build();

        $this->view->assign('juryLoginLink', $juryLoginLink);
        $this->view->assign('juryReference', $juryReference);
        $this->view->assign(
            'approvedRegistrations',
            $this->registerRepository->findAdminApprovedByCompetition($juryReference->getCompetition())
        );
    }



    /**
     * action edit
     *
     * Hint: The juryReference record is created via cronjob (juryNotify)
     *
     * @param string $token
     * @return string|object|null|void
     */
    public function editAction(string $token)
    {
        $juryReference = $this->juryReferenceRepository->findByInviteToken($token)->getFirst();

        DebuggerUtility::var_dump($juryReference);

        // @toDo: Handle if UserReference is not found

        // @toDo: Handle if UserReference is already confirmed (or expired?)
        if ($juryReference->getGuestUser() instanceof GuestUser) {

            $err = LocalizationUtility::translate('juryController.error.tokenAlreadyUsed', 'rkw_competition');
            $this->view->assign('errorMessage', $err);
        }

        $this->view->assign('juryReference', $juryReference);
    }



    /**
     * action update
     *
     * @param \RKW\RkwCompetition\Domain\Model\JuryReference $juryReference
     * @param int $consentAsJuryMember
     * @return string|object|null|void
     */
    public function updateAction(
        \RKW\RkwCompetition\Domain\Model\JuryReference $juryReference = null,
        int $consentAsJuryMember = 0
    )
    {

        // @toDo: do some security check

        $errorMessage = '';

        if (!$consentAsJuryMember) {
            $errorMessage = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'juryController.message.consent',
                'rkw_competition'
            );

        }
        if ($juryReference->getConsentedAt()) {
            $errorMessage = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'juryController.message.alreadyConsented',
                'rkw_competition'
            );
        }

        if ($errorMessage) {
            $this->addFlashMessage(
                $errorMessage,
                '',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );

            $this->redirect(
                'edit',
                null,
                null,
                ['juryReference' => $juryReference]
            );
        }

        $juryReference->setConsentedAt(time());

        // Create GuestUser
        $newGuestUser = GeneralUtility::makeInstance(GuestUser::class);
        $this->guestUserRegistration->setFrontendUser($newGuestUser);
        $this->guestUserRegistration->startRegistration();

    //    DebuggerUtility::var_dump($newGuestUser); exit;
        $newGuestUser->setDisable(false);
        // set lifetime (end of competition jury period)
        $newGuestUser->setEndtime($juryReference->getCompetition()->getJuryAccessEnd()->getTimestamp());
        $juryReference->setGuestUser($newGuestUser);


        // @toDo: Inhaltlich betrachtet benötigen wir keinen Login. Wir könnten nur mit dem User-Token
        // -> Jedoch erfolgt wohl auch über "->startRegistration" bereits ein login (wirft Fehler, wenn man bereits eingeloggt ist)
//        $_POST['logintype'] = 'login';
//        $_POST['user'] = $newGuestUser->getUsername();
//        $_POST['pass'] = '';
//
//        // authenticate new user
//        $authService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(FrontendUserAuthentication::class);
//        $authService->start();


        // add as confirmed jury member
        $juryReference->getCompetition()->addJuryMemberConfirmed($juryReference);
        $this->competitionRepository->update($juryReference->getCompetition());
        $this->juryReferenceRepository->update($juryReference);

        $this->addFlashMessage(
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'juryController.message.updated',
                'rkw_competition'
            )
        );

        // go to campaign view
        $this->redirect('show', null, null, ['userToken' => $newGuestUser->getUsername()]);
    }



    /**
     * action delete
     *
     * @deprecated Not used yet
     *
     * @todo Bisher keine Vorgabe, dass Jurymitglieder sich selbst rauslöschen können. Könnte chaotisch werden!
     *
     * @param \RKW\RkwCompetition\Domain\Model\JuryReference $juryReference
     * @return string|object|null|void
     */
    public function deleteAction(\RKW\RkwCompetition\Domain\Model\JuryReference $juryReference)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->juryReferenceRepository->remove($juryReference);
        $this->redirect('list');
    }
}
