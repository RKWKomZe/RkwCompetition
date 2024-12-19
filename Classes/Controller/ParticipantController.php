<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Controller;


use Madj2k\FeRegister\Domain\Model\FrontendUser;
use Madj2k\FeRegister\Utility\FrontendUserSessionUtility;
use Madj2k\FeRegister\Utility\FrontendUserUtility;
use RKW\RkwCompetition\Utility\RegisterUtility;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
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
 * ParticipantController
 */
class ParticipantController extends \RKW\RkwCompetition\Controller\AbstractController
{

    /**
     * action list
     *
     * @return void
     * @throws AspectNotFoundException
     */
    public function listAction()
    {
        if (!$this->getFrontendUser()) {

            $this->addFlashMessage(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                    'participantController.message.error.notPermitted', 'rkw_competition'
                ),
                '',
                \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR
            );
        } else {

            $registerList = $this->registerRepository->findByFrontendUser($this->getFrontendUser());

            $this->view->assign('registerList', $registerList);
        }

    }


}
