<?php
namespace RKW\RkwCompetition\Controller;

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

use Madj2k\FeRegister\Domain\Model\FrontendUser;
use Madj2k\FeRegister\Utility\FrontendUserSessionUtility;
use Madj2k\FeRegister\Utility\FrontendUserUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Class AbstractController
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @package RKW_RkwCompetition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class AbstractController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
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
     * @var \Madj2k\FeRegister\Domain\Repository\FrontendUserRepository
     */
    protected $frontendUserRepository = null;

    /**
     * @param \Madj2k\FeRegister\Domain\Repository\FrontendUserRepository $frontendUserRepository
     */
    public function injectFrontendUserRepository(\Madj2k\FeRegister\Domain\Repository\FrontendUserRepository $frontendUserRepository)
    {
        $this->frontendUserRepository = $frontendUserRepository;
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
     * @var \Madj2k\FeRegister\Domain\Model\FrontendUser
     */
    protected ?FrontendUser $frontendUser = null;


    /**
     * Id of logged User
     *
     * @return int
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    protected function getFrontendUserId(): int
    {
        if (
            ($frontendUser = FrontendUserSessionUtility::getLoggedInUser())
            && (! FrontendUserUtility::isGuestUser($frontendUser))
        ){
            return $frontendUser->getUid();
        }

        return 0;
    }


    /**
     * Returns current logged-in user object
     *
     * @return \Madj2k\FeRegister\Domain\Model\FrontendUser|null
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    protected function getFrontendUser():? FrontendUser
    {
        /** @var \Madj2k\FeRegister\Domain\Repository\FrontendUserRepository $frontendUserRepository */
        $this->frontendUser = $this->frontendUserRepository->findByIdentifier($this->getFrontendUserId());

        if ($this->frontendUser instanceof \TYPO3\CMS\Extbase\Domain\Model\FrontendUser) {
            return $this->frontendUser;
        }

        return null;
    }

}
