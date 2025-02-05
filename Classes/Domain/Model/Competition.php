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
     * @var \DateTime
     */
    protected $crdate = null;

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
     * recordRemovalDate
     *
     * @var \DateTime
     */
    protected $recordRemovalDate = '';

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
     * reminderIncompleteMailTstamp
     *
     * @var integer
     */
    protected $reminderIncompleteMailTstamp = 0;

    /**
     * reminderCleanupMailTstamp
     *
     * @var integer
     */
    protected $reminderCleanupMailTstamp = 0;

    /**
     * reminderJuryMailTstamp
     *
     * @var integer
     */
    protected $reminderJuryMailTstamp = 0;

    /**
     * closingDayMailTstamp
     *
     * @var integer
     */
    protected $closingDayMailTstamp = 0;

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
     * ownCloudFolderLink
     *
     * @var string
     */
    protected $ownCloudFolderLink = '';

    /**
     * adminMember
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Madj2k\FeRegister\Domain\Model\BackendUser>
     */
    protected $adminMember = null;

    /**
     * juryMemberCandidate
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwCompetition\Domain\Model\FrontendUser>
     */
    protected $juryMemberCandidate = null;

    /**
     * juryMemberConfirmed
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwCompetition\Domain\Model\FrontendUser>
     */
    protected $juryMemberConfirmed = null;

    /**
     * groupForJury
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup
     */
    protected $groupForJury = null;

    /**
     * groupForUser
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup
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
        $this->juryMemberCandidate = $this->juryMemberCandidate ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->juryMemberConfirmed = $this->juryMemberCandidate ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->register = $this->register ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->sectors = $this->sectors ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the creation date
     *
     * @return \DateTime
     */
    public function getCrdate()
    {
        return $this->crdate;
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
     * Returns the recordRemovalDate
     *
     * @return \DateTime
     */
    public function getRecordRemovalDate()
    {
        return $this->recordRemovalDate;
    }

    /**
     * Sets the recordRemovalDate
     *
     * @param \DateTime $recordRemovalDate
     * @return void
     */
    public function setRecordRemovalDate(\DateTime $recordRemovalDate)
    {
        $this->recordRemovalDate = $recordRemovalDate;
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
     * Returns the reminderIncompleteMailTstamp
     *
     * @return int $reminderIncompleteMailTstamp
     */
    public function getReminderIncompleteMailTstamp()
    {
        return $this->reminderIncompleteMailTstamp;
    }

    /**
     * Sets the reminderIncompleteMailTstamp
     *
     * @param int $reminderIncompleteMailTstamp
     * @return void
     */
    public function setReminderIncompleteMailTstamp($reminderIncompleteMailTstamp)
    {
        $this->reminderIncompleteMailTstamp = $reminderIncompleteMailTstamp;
    }

    /**
     * Returns the reminderCleanupMailTstamp
     *
     * @return int $reminderCleanupMailTstamp
     */
    public function getReminderCleanupMailTstamp()
    {
        return $this->reminderCleanupMailTstamp;
    }

    /**
     * Sets the reminderCleanupMailTstamp
     *
     * @param int $reminderCleanupMailTstamp
     * @return void
     */
    public function setReminderCleanupMailTstamp($reminderCleanupMailTstamp)
    {
        $this->reminderCleanupMailTstamp = $reminderCleanupMailTstamp;
    }

    /**
     * Returns the reminderJuryMailTstamp
     *
     * @return int $reminderJuryMailTstamp
     */
    public function getReminderJuryMailTstamp()
    {
        return $this->reminderJuryMailTstamp;
    }

    /**
     * Sets the reminderJuryMailTstamp
     *
     * @param int $reminderJuryMailTstamp
     * @return void
     */
    public function setReminderJuryMailTstamp($reminderJuryMailTstamp)
    {
        $this->reminderJuryMailTstamp = $reminderJuryMailTstamp;
    }

    /**
     * Returns the closingDayMailTstamp
     *
     * @return int $closingDayMailTstamp
     */
    public function getClosingDayMailTstamp()
    {
        return $this->closingDayMailTstamp;
    }

    /**
     * Sets the closingDayMailTstamp
     *
     * @param int $closingDayMailTstamp
     * @return void
     */
    public function setClosingDayMailTstamp($closingDayMailTstamp)
    {
        $this->closingDayMailTstamp = $closingDayMailTstamp;
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
     * @return string
     */
    public function getOwnCloudFolderLink(): string
    {
        return $this->ownCloudFolderLink;
    }

    /**
     * @param string $ownCloudFolderLink
     * @return void
     */
    public function setOwnCloudFolderLink(string $ownCloudFolderLink): void
    {
        $this->ownCloudFolderLink = $ownCloudFolderLink;
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
     * @param \RKW\RkwCompetition\Domain\Model\FrontendUser $juryMemberCandidate
     * @return void
     */
    public function addJuryMemberCandidate(\RKW\RkwCompetition\Domain\Model\FrontendUser $juryMemberCandidate)
    {
        $this->juryMemberCandidate->attach($juryMemberCandidate);
    }

    /**
     * Removes a FrontendUser
     *
     * @param \RKW\RkwCompetition\Domain\Model\FrontendUser $juryMemberCandidateToRemove The FrontendUser to be removed
     * @return void
     */
    public function removeJuryMemberCandidate(\RKW\RkwCompetition\Domain\Model\FrontendUser $juryMemberCandidateToRemove)
    {
        $this->juryMemberCandidate->detach($juryMemberCandidateToRemove);
    }

    /**
     * Returns the juryMemberCandidate
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwCompetition\Domain\Model\FrontendUser>
     */
    public function getJuryMemberCandidate()
    {
        return $this->juryMemberCandidate;
    }

    /**
     * Sets the juryMemberCandidate
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwCompetition\Domain\Model\FrontendUser> $juryMemberCandidate
     * @return void
     */
    public function setJuryMemberCandidate(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $juryMemberCandidate)
    {
        $this->juryMemberCandidate = $juryMemberCandidate;
    }

    /**
     * Adds a FrontendUser
     *
     * @param \RKW\RkwCompetition\Domain\Model\FrontendUser $juryMemberConfirmed
     * @return void
     */
    public function addJuryMemberConfirmed(\RKW\RkwCompetition\Domain\Model\FrontendUser $juryMemberConfirmed)
    {
        $this->juryMemberConfirmed->attach($juryMemberConfirmed);
    }

    /**
     * Removes a FrontendUser
     *
     * @param \RKW\RkwCompetition\Domain\Model\FrontendUser $juryMemberConfirmedToRemove The FrontendUser to be removed
     * @return void
     */
    public function removeJuryMemberConfirmed(\RKW\RkwCompetition\Domain\Model\FrontendUser $juryMemberConfirmedToRemove)
    {
        $this->juryMemberConfirmed->detach($juryMemberConfirmedToRemove);
    }

    /**
     * Returns the juryMemberConfirmed
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwCompetition\Domain\Model\FrontendUser>
     */
    public function getJuryMemberConfirmed()
    {
        return $this->juryMemberConfirmed;
    }

    /**
     * Sets the juryMemberConfirmed
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\RKW\RkwCompetition\Domain\Model\FrontendUser> $juryMemberConfirmed
     * @return void
     */
    public function setJuryMemberConfirmed(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $juryMemberConfirmed)
    {
        $this->juryMemberConfirmed = $juryMemberConfirmed;
    }

    /**
     * Returns the groupForJury
     *
     * @return \RKW\RkwCompetition\Domain\Model\FrontendUserGroup
     */
    public function getGroupForJury()
    {
        return $this->groupForJury;
    }

    /**
     * Sets the groupForJury
     *
     * @param \RKW\RkwCompetition\Domain\Model\FrontendUserGroup $groupForJury
     * @return void
     */
    public function setGroupForJury(\RKW\RkwCompetition\Domain\Model\FrontendUserGroup $groupForJury)
    {
        $this->groupForJury = $groupForJury;
    }

    /**
     * Returns the groupForUser
     *
     * @return \RKW\RkwCompetition\Domain\Model\FrontendUserGroup
     */
    public function getGroupForUser()
    {
        return $this->groupForUser;
    }

    /**
     * Sets the groupForUser
     *
     * @param \RKW\RkwCompetition\Domain\Model\FrontendUserGroup $groupForUser
     * @return void
     */
    public function setGroupForUser(\RKW\RkwCompetition\Domain\Model\FrontendUserGroup $groupForUser)
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
