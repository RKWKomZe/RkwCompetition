<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Domain\Model;


/**
 * This file is part of the "RKW Competition" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 Maximilian Fäßler <maximilian@faesslerweb.de>, RKW Kompetenzzentrum
 */

/**
 * Competition
 */
class Competition extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * registerStart
     *
     * @var \DateTime
     */
    protected $registerStart = null;

    /**
     * registerEnd
     *
     * @var \DateTime
     */
    protected $registerEnd = null;

    /**
     * juryAccessEnd
     *
     * @var \DateTime
     */
    protected $juryAccessEnd = '';

    /**
     * fileRemovalEnd
     *
     * @var \DateTime
     */
    protected $fileRemovalEnd = '';

    /**
     * juryAddData
     *
     * @var string
     */
    protected $juryAddData = '';

    /**
     * linkJuryDeclarationConfident
     *
     * @var string
     */
    protected $linkJuryDeclarationConfident = '';

    /**
     * allowTeamParticipation
     *
     * @var bool
     */
    protected $allowTeamParticipation = false;

    /**
     * reminderInterval
     *
     * @var int
     */
    protected $reminderInterval = 0;

    /**
     * linkCondParticipation
     *
     * @var string
     */
    protected $linkCondParticipation = '';

    /**
     * linkPrivacy
     *
     * @var string
     */
    protected $linkPrivacy = '';

    /**
     * adminMember
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Madj2k\FeRegister\Domain\Model\BackendUser>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $adminMember = null;

    /**
     * juryMember
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Madj2k\FeRegister\Domain\Model\FrontendUser>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $juryMember = null;

    /**
     * groupForJury
     *
     * @var \Madj2k\FeRegister\Domain\Model\FrontendUserGroup
     */
    protected $groupForJury = null;

    /**
     * groupForUser
     *
     * @var \Madj2k\FeRegister\Domain\Model\FrontendUserGroup
     */
    protected $groupForUser = null;

    /**
     * sectors
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwCompetition\Domain\Model\Sector>
     */
    protected $sectors = null;

    /**
     * register
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwCompetition\Domain\Model\Register>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $register = null;

    /**
     * __construct
     */
    public function __construct()
    {

        // Do not remove the next line: It would break the functionality
        $this->initializeObject();
    }

    /**
     * Initializes all ObjectStorage properties when model is reconstructed from DB (where __construct is not called)
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->adminMember = $this->adminMember ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->juryMember = $this->juryMember ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->register = $this->register ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->sectors = $this->sector ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Returns the registerStart
     *
     * @return \DateTime
     */
    public function getRegisterStart()
    {
        return $this->registerStart;
    }

    /**
     * Sets the registerStart
     *
     * @param \DateTime $registerStart
     * @return void
     */
    public function setRegisterStart(\DateTime $registerStart)
    {
        $this->registerStart = $registerStart;
    }

    /**
     * Returns the registerEnd
     *
     * @return \DateTime
     */
    public function getRegisterEnd()
    {
        return $this->registerEnd;
    }

    /**
     * Sets the registerEnd
     *
     * @param \DateTime $registerEnd
     * @return void
     */
    public function setRegisterEnd(\DateTime $registerEnd)
    {
        $this->registerEnd = $registerEnd;
    }

    /**
     * Returns the juryAccessEnd
     *
     * @return \DateTime
     */
    public function getJuryAccessEnd()
    {
        return $this->juryAccessEnd;
    }

    /**
     * Sets the juryAccessEnd
     *
     * @param \DateTime $juryAccessEnd
     * @return void
     */
    public function setJuryAccessEnd(\DateTime $juryAccessEnd)
    {
        $this->juryAccessEnd = $juryAccessEnd;
    }

    /**
     * Returns the fileRemovalEnd
     *
     * @return \DateTime
     */
    public function getFileRemovalEnd()
    {
        return $this->fileRemovalEnd;
    }

    /**
     * Sets the fileRemovalEnd
     *
     * @param \DateTime $fileRemovalEnd
     * @return void
     */
    public function setFileRemovalEnd(\DateTime $fileRemovalEnd)
    {
        $this->fileRemovalEnd = $fileRemovalEnd;
    }

    /**
     * Returns the juryAddData
     *
     * @return string
     */
    public function getJuryAddData()
    {
        return $this->juryAddData;
    }

    /**
     * Sets the juryAddData
     *
     * @param string $juryAddData
     * @return void
     */
    public function setJuryAddData(string $juryAddData)
    {
        $this->juryAddData = $juryAddData;
    }

    /**
     * Returns the linkJuryDeclarationConfident
     *
     * @return string
     */
    public function getLinkJuryDeclarationConfident()
    {
        return $this->linkJuryDeclarationConfident;
    }

    /**
     * Sets the linkJuryDeclarationConfident
     *
     * @param string $linkJuryDeclarationConfident
     * @return void
     */
    public function setLinkJuryDeclarationConfident(string $linkJuryDeclarationConfident)
    {
        $this->linkJuryDeclarationConfident = $linkJuryDeclarationConfident;
    }

    /**
     * Returns the allowTeamParticipation
     *
     * @return bool
     */
    public function getAllowTeamParticipation()
    {
        return $this->allowTeamParticipation;
    }

    /**
     * Sets the allowTeamParticipation
     *
     * @param bool $allowTeamParticipation
     * @return void
     */
    public function setAllowTeamParticipation(bool $allowTeamParticipation)
    {
        $this->allowTeamParticipation = $allowTeamParticipation;
    }

    /**
     * Returns the boolean state of allowTeamParticipation
     *
     * @return bool
     */
    public function isAllowTeamParticipation()
    {
        return $this->allowTeamParticipation;
    }

    /**
     * Returns the reminderInterval
     *
     * @return int
     */
    public function getReminderInterval()
    {
        return $this->reminderInterval;
    }

    /**
     * Sets the reminderInterval
     *
     * @param int $reminderInterval
     * @return void
     */
    public function setReminderInterval(int $reminderInterval)
    {
        $this->reminderInterval = $reminderInterval;
    }

    /**
     * Returns the linkCondParticipation
     *
     * @return string
     */
    public function getLinkCondParticipation()
    {
        return $this->linkCondParticipation;
    }

    /**
     * Sets the linkCondParticipation
     *
     * @param string $linkCondParticipation
     * @return void
     */
    public function setLinkCondParticipation(string $linkCondParticipation)
    {
        $this->linkCondParticipation = $linkCondParticipation;
    }

    /**
     * Returns the linkPrivacy
     *
     * @return string
     */
    public function getLinkPrivacy()
    {
        return $this->linkPrivacy;
    }

    /**
     * Sets the linkPrivacy
     *
     * @param string $linkPrivacy
     * @return void
     */
    public function setLinkPrivacy(string $linkPrivacy)
    {
        $this->linkPrivacy = $linkPrivacy;
    }

    /**
     * Adds a BackendUser
     *
     * @param \Madj2k\FeRegister\Domain\Model\BackendUser $adminMember
     * @return void
     */
    public function addAdminMember(\Madj2k\FeRegister\Domain\Model\BackendUser $adminMember)
    {
        $this->adminMember->attach($adminMember);
    }

    /**
     * Removes a BackendUser
     *
     * @param \Madj2k\FeRegister\Domain\Model\BackendUser $adminMemberToRemove The BackendUser to be removed
     * @return void
     */
    public function removeAdminMember(\Madj2k\FeRegister\Domain\Model\BackendUser $adminMemberToRemove)
    {
        $this->adminMember->detach($adminMemberToRemove);
    }

    /**
     * Returns the adminMember
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Madj2k\FeRegister\Domain\Model\BackendUser>
     */
    public function getAdminMember()
    {
        return $this->adminMember;
    }

    /**
     * Sets the adminMember
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Madj2k\FeRegister\Domain\Model\BackendUser> $adminMember
     * @return void
     */
    public function setAdminMember(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $adminMember)
    {
        $this->adminMember = $adminMember;
    }

    /**
     * Adds a FrontendUser
     *
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUser $juryMember
     * @return void
     */
    public function addJuryMember(\Madj2k\FeRegister\Domain\Model\FrontendUser $juryMember)
    {
        $this->juryMember->attach($juryMember);
    }

    /**
     * Removes a FrontendUser
     *
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUser $juryMemberToRemove The FrontendUser to be removed
     * @return void
     */
    public function removeJuryMember(\Madj2k\FeRegister\Domain\Model\FrontendUser $juryMemberToRemove)
    {
        $this->juryMember->detach($juryMemberToRemove);
    }

    /**
     * Returns the juryMember
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Madj2k\FeRegister\Domain\Model\FrontendUser>
     */
    public function getJuryMember()
    {
        return $this->juryMember;
    }

    /**
     * Sets the juryMember
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Madj2k\FeRegister\Domain\Model\FrontendUser> $juryMember
     * @return void
     */
    public function setJuryMember(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $juryMember)
    {
        $this->juryMember = $juryMember;
    }

    /**
     * Returns the groupForJury
     *
     * @return \Madj2k\FeRegister\Domain\Model\FrontendUserGroup
     */
    public function getGroupForJury()
    {
        return $this->groupForJury;
    }

    /**
     * Sets the groupForJury
     *
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUserGroup $groupForJury
     * @return void
     */
    public function setGroupForJury(\Madj2k\FeRegister\Domain\Model\FrontendUserGroup $groupForJury)
    {
        $this->groupForJury = $groupForJury;
    }

    /**
     * Returns the groupForUser
     *
     * @return \Madj2k\FeRegister\Domain\Model\FrontendUserGroup
     */
    public function getGroupForUser()
    {
        return $this->groupForUser;
    }

    /**
     * Sets the groupForUser
     *
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUserGroup $groupForUser
     * @return void
     */
    public function setGroupForUser(\Madj2k\FeRegister\Domain\Model\FrontendUserGroup $groupForUser)
    {
        $this->groupForUser = $groupForUser;
    }

    /**
     * Adds a sector
     *
     * @param \RKW\RkwCompetition\Domain\Model\Sector $sector
     * @return void
     */
    public function addSector(\RKW\RkwCompetition\Domain\Model\Sector $sector)
    {
        $this->sectors->attach($sector);
    }

    /**
     * Removes a sector
     *
     * @param \RKW\RkwCompetition\Domain\Model\Sector $sectorToRemove The sector to be removed
     * @return void
     */
    public function removeSector(\RKW\RkwCompetition\Domain\Model\Sector $sectorToRemove)
    {
        $this->sectors->detach($sectorToRemove);
    }

    /**
     * Returns the sectors
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwCompetition\Domain\Model\Sector>
     */
    public function getSectors()
    {
        return $this->sectors;
    }

    /**
     * Sets the sectors
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwCompetition\Domain\Model\Sector> $sectors
     * @return void
     */
    public function setSectors(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $sectors)
    {
        $this->sectors = $sectors;
    }

    /**
     * Adds a Register
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $register
     * @return void
     */
    public function addRegister(\RKW\RkwCompetition\Domain\Model\Register $register)
    {
        $this->register->attach($register);
    }

    /**
     * Removes a Register
     *
     * @param \RKW\RkwCompetition\Domain\Model\Register $registerToRemove The Register to be removed
     * @return void
     */
    public function removeRegister(\RKW\RkwCompetition\Domain\Model\Register $registerToRemove)
    {
        $this->register->detach($registerToRemove);
    }

    /**
     * Returns the register
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwCompetition\Domain\Model\Register>
     */
    public function getRegister()
    {
        return $this->register;
    }

    /**
     * Sets the register
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwCompetition\Domain\Model\Register> $register
     * @return void
     */
    public function setRegister(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $register)
    {
        $this->register = $register;
    }
}
