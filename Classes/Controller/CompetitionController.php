<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Controller;


use Madj2k\CoreExtended\Utility\GeneralUtility;
use Madj2k\FeRegister\Domain\Model\GuestUser;
use Madj2k\FeRegister\Registration\GuestUserRegistration;
use RKW\RkwCompetition\Api\OwnCloud;
use RKW\RkwCompetition\Utility\OwnCloudUtility;
use Solarium\Component\Debug;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * This file is part of the "RKW Competition" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 Maximilian Fäßler <maximilian@faesslerweb.de>, RKW Kompetenzzentrum
 */

/**
 * CompetitionController
 */
class CompetitionController extends \RKW\RkwCompetition\Controller\AbstractController
{

    /**
     * competitionRepository
     *
     * @var \RKW\RkwCompetition\Domain\Repository\CompetitionRepository
     */
    protected $competitionRepository = null;

    /**
     * @param \RKW\RkwCompetition\Domain\Repository\CompetitionRepository $competitionRepository
     */
    public function injectCompetitionRepository(\RKW\RkwCompetition\Domain\Repository\CompetitionRepository $competitionRepository)
    {
        $this->competitionRepository = $competitionRepository;
    }

    /**
     * action show
     *
     * @param \RKW\RkwCompetition\Domain\Model\Competition|null $competition
     * @return void
     */
    public function showAction(\RKW\RkwCompetition\Domain\Model\Competition $competition = null)
    {

//        $newGuestUser = GeneralUtility::makeInstance(GuestUser::class);
//        $guestUserRegistration = GeneralUtility::makeInstance(GuestUserRegistration::class);
//        $guestUserRegistration->setFrontendUser($newGuestUser);
//        $guestUserRegistration->startRegistration();

//
//        $userId = 8;
//        $userName = "test" . $userId;
//
//        $ownCloud = \Madj2k\CoreExtended\Utility\GeneralUtility::makeInstance(OwnCloud::class);
//
//        $competitionFolderName = 'competition_uid_79';
//        // @toDo: User folder name are cryptic (the jury member have to distinguish them)
//        $userFolderName = 'feuser_uid_56' . $userId;
//
//        // 6.1 create folder
//        $folderCreatePath = GeneralUtility::trimExplode('/', $this->settings['api']['ownCloud']['folderStructure']['basePath'], true);
//        $ownCloud->getWebDavApi()->addFolderRecursive(
//            array_merge($folderCreatePath, [$competitionFolderName], [$userFolderName])
//        );
//
//        // 6.2 create user & get User
//        //$ownCloud->getUsersApi()->addUser($userName, 'test');
//
//        // @toDo: Simply save link & PW inside the database to show it inside the login area?
//
//        // 6.3 create share for user to upload stuff to specific folder
//        $result = $ownCloud->getShareApi()->createShare(
//            $userFolderName,
//            array_merge($folderCreatePath, [$competitionFolderName], [$userFolderName]),
//            3,
//            '',
//            true,
//            'publicSecret',
//            15,
//            '2025-02-28'
//        );
//
//
//        DebuggerUtility::var_dump($result['url']); exit;
//

//        // 6.4 create share for jury member to show the whole competition folder
//        $ownCloud->getShareApi()->createShare(
//            $competitionFolderName,
//            array_merge($folderCreatePath, [$competitionFolderName]),
//            3,
//            '',
//            true,
//            'publicSecret',
//            15,
//            '2025-04-17'
//        );



//        $ownCloud = GeneralUtility::makeInstance(OwnCloud::class);

       // DebuggerUtility::var_dump($ownCloudApi->getUser('Hannes'));
        //DebuggerUtility::var_dump($ownCloudApi->editUser('Hannes', 'email', 'hannes@rkw.de'));

        //DebuggerUtility::var_dump($ownCloud->getUsersApi()->addUser('Hannes', 'password'));

        //DebuggerUtility::var_dump($ownCloud->getUsersApi()->addToGroup('Hannes', 'Teilnehmer'));

        //$ownCloud->getWebDavApi()->addFolder(['Documents', 'Something']);

//        $folderCreatePath = GeneralUtility::trimExplode('/', $this->settings['api']['ownCloud']['folderStructure']['basePath'], true);
//        $folderCreatePath[] = 'competition_uid_12345';
//        $folderCreatePath[] = 'feuser_uid_987';

        //$ownCloud->getWebDavApi()->addFolder($folderCreatePath);

//        $ownCloud->getWebDavApi()->addFolderRecursive($folderCreatePath);

        //$ownCloud->getWebDavApi()->removeFileOrFolder($folderCreatePath);

       // $ownCloud->getShareApi()->createShare('feuser_uid_987', $folderCreatePath, 0, 'Hannes');

        //$ownCloud->getShareApi()->getAllShares(['rkw_competition', 'competition_uid_12345']);

    //    $ownCloud->getShareApi()->getShare(1);

    //    $ownCloud->getShareApi()->deleteShare(1);

        //DebuggerUtility::var_dump($ownCloudApi->getUserList('Hannes1'));


        /*
            POST (new user)
            curl -X POST http://admin:secret@example.com/ocs/v1.php/cloud/users \
            -d userid="Frank" \
            -d password="frankspassword"

            PUT (edit user)
            curl -X PUT http://admin:secret@example.com/ocs/v1.php/cloud/users/Frank \
            -d key="email" \
            -d value="franksnewemail@example.org"

         */
//
//        $compTest = $this->competitionRepository->findByIdentifier(intval($this->settings['selectedCompetition']));
//
//        DebuggerUtility::var_dump($compTest);
//
//        $compTest->setOwnCloudFolderLink('http://test.de');
//
//
//        $this->competitionRepository->update($compTest);
//
//        $this->persistenceManager->persistAll();
//
//        DebuggerUtility::var_dump($compTest);

        $this->view->assign(
            'competition', $competition ?: $this->competitionRepository->findByIdentifier(intval($this->settings['selectedCompetition']))
        );
    }
}
