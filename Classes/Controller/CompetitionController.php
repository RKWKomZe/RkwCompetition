<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Controller;


use Madj2k\CoreExtended\Utility\GeneralUtility;
use RKW\RkwCompetition\Api\OwnCloud;
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

        $ownCloud = GeneralUtility::makeInstance(OwnCloud::class);

       // DebuggerUtility::var_dump($ownCloudApi->getUser('Hannes'));
        //DebuggerUtility::var_dump($ownCloudApi->editUser('Hannes', 'email', 'hannes@rkw.de'));

        //DebuggerUtility::var_dump($ownCloud->getUsersApi()->addUser('Hannes', 'password'));

        //DebuggerUtility::var_dump($ownCloud->getUsersApi()->addToGroup('Hannes', 'Teilnehmer'));

        //$ownCloud->getWebDavApi()->addFolder(['Documents', 'Something']);

        $folderCreatePath = GeneralUtility::trimExplode('/', $this->settings['api']['ownCloud']['folderStructure']['basePath'], true);
        $folderCreatePath[] = 'competition_uid_12345';
        //$folderCreatePath[] = 'feuser_uid_987';

        //$ownCloud->getWebDavApi()->addFolder($folderCreatePath);

        //$ownCloud->getWebDavApi()->addFolderRecursive($folderCreatePath);

        //$ownCloud->getWebDavApi()->removeFileOrFolder($folderCreatePath);

        $ownCloud->getShareApi()->createShare('feuser_uid_987', $folderCreatePath, 0, 'Hannes');


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


        $this->view->assign(
            'competition', $competition ?: $this->competitionRepository->findByIdentifier(intval($this->settings['selectedCompetition']))
        );
    }
}
