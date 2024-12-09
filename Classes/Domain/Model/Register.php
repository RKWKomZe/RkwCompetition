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
 * Register
 */
class Register extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * salutation
     *
     * @var string
     */
    protected $salutation = '';

    /**
     * title
     *
     * @var int
     */
    protected $title = 0;

    /**
     * firstName
     *
     * @var string
     */
    protected $firstName = '';

    /**
     * lastName
     *
     * @var string
     */
    protected $lastName = '';

    /**
     * institution
     *
     * @var string
     */
    protected $institution = '';

    /**
     * address
     *
     * @var string
     */
    protected $address = '';

    /**
     * zip
     *
     * @var int
     */
    protected $zip = 0;

    /**
     * city
     *
     * @var string
     */
    protected $city = '';

    /**
     * telephone
     *
     * @var string
     */
    protected $telephone = '';

    /**
     * email
     *
     * @var string
     */
    protected $email = '';

    /**
     * contributionTitle
     *
     * @var string
     */
    protected $contributionTitle = '';

    /**
     * typeOfWork
     *
     * @var int
     */
    protected $typeOfWork = 0;

    /**
     * remark
     *
     * @var string
     */
    protected $remark = '';

    /**
     * privacy
     *
     * @var int
     */
    protected $privacy = 0;

    /**
     * conditionsOfParticipation
     *
     * @var int
     */
    protected $conditionsOfParticipation = 0;

    /**
     * isGroupWork
     *
     * @var int
     */
    protected $isGroupWork = 0;

    /**
     * groupWorkInsurance
     *
     * @var int
     */
    protected $groupWorkInsurance = 0;

    /**
     * groupWorkAddPersons
     *
     * @var string
     */
    protected $groupWorkAddPersons = '';

    /**
     * userSubmittedAt
     *
     * @var int
     */
    protected $userSubmittedAt = 0;

    /**
     * adminApproved
     *
     * @var boolean
     */
    protected $adminApproved = 0;

    /**
     * adminApprovedAt
     *
     * @var int
     */
    protected $adminApprovedAt = 0;

    /**
     * adminRefused
     *
     * @var boolean
     */
    protected $adminRefused = 0;

    /**
     * adminRefusedAt
     *
     * @var int
     */
    protected $adminRefusedAt = 0;

    /**
     * adminRefusedText
     *
     * @var string
     */
    protected $adminRefusedText = '';

    /**
     * uniqueId
     *
     * @var string
     */
    protected $uniqueId = '';

    /**
     * sector
     *
     * @var \RKW\RkwCompetition\Domain\Model\Sector
     */
    protected $sector = null;

    /**
     * upload
     *
     * @var \RKW\RkwCompetition\Domain\Model\Upload
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $upload = null;

    /**
     * frontendUser
     *
     * @var \Madj2k\FeRegister\Domain\Model\FrontendUser
     */
    protected $frontendUser = null;

    /**
     * competition
     *
     * @var \RKW\RkwCompetition\Domain\Model\Competition
     */
    protected $competition = null;

    /**
     * adminApprovedBy
     *
     * @var \RKW\RkwCompetition\Domain\Model\BackendUser
     */
    protected $adminApprovedBy = null;

    /**
     * adminRefusedBy
     *
     * @var \RKW\RkwCompetition\Domain\Model\BackendUser
     */
    protected $adminRefusedBy = null;

    /**
     * @var string
     */
    protected $captchaResponse;

    /**
     * Sets the captchaResponse
     *
     * @param string $captchaResponse
     * @return void
     */
    public function setCaptchaResponse($captchaResponse) {
        $this->captchaResponse = $captchaResponse;
    }

    /**
     * Getter for captchaResponse
     *
     * @return string
     */
    public function getCaptchaResponse() {
        return $this->captchaResponse;
    }

    /**
     * Returns the salutation
     *
     * @return string
     */
    public function getSalutation()
    {
        return $this->salutation;
    }

    /**
     * Sets the salutation
     *
     * @param string $salutation
     * @return void
     */
    public function setSalutation(string $salutation)
    {
        $this->salutation = $salutation;
    }

    /**
     * Returns the title
     *
     * @return int
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param int|null $title
     * @return void
     */
    public function setTitle(int $title = null)
    {
        $this->title = (int) $title;
    }

    /**
     * Returns the firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Sets the firstName
     *
     * @param string $firstName
     * @return void
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Returns the lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Sets the lastName
     *
     * @param string $lastName
     * @return void
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Returns the institution
     *
     * @return string
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Sets the institution
     *
     * @param string $institution
     * @return void
     */
    public function setInstitution(string $institution)
    {
        $this->institution = $institution;
    }

    /**
     * Returns the address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Sets the address
     *
     * @param string $address
     * @return void
     */
    public function setAddress(string $address)
    {
        $this->address = $address;
    }

    /**
     * Returns the zip
     *
     * @return int
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Sets the zip
     *
     * @param int|null $zip
     * @return void
     */
    public function setZip(int $zip = null)
    {
        $this->zip = (int) $zip;
    }

    /**
     * Returns the city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the city
     *
     * @param string $city
     * @return void
     */
    public function setCity(string $city)
    {
        $this->city = $city;
    }

    /**
     * Returns the telephone
     *
     * @return string $telephone
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Sets the telephone
     *
     * @param string $telephone
     * @return void
     */
    public function setTelephone(string $telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * Returns the email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email
     *
     * @param string $email
     * @return void
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * Returns the contributionTitle
     *
     * @return string
     */
    public function getContributionTitle()
    {
        return $this->contributionTitle;
    }

    /**
     * Sets the contributionTitle
     *
     * @param string $contributionTitle
     * @return void
     */
    public function setContributionTitle(string $contributionTitle)
    {
        $this->contributionTitle = $contributionTitle;
    }

    /**
     * Returns the typeOfWork
     *
     * @return int
     */
    public function getTypeOfWork()
    {
        return $this->typeOfWork;
    }

    /**
     * Sets the typeOfWork
     *
     * @param int $typeOfWork
     * @return void
     */
    public function setTypeOfWork(int $typeOfWork)
    {
        $this->typeOfWork = $typeOfWork;
    }

    /**
     * Returns the remark
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Sets the remark
     *
     * @param string $remark
     * @return void
     */
    public function setRemark(string $remark)
    {
        $this->remark = $remark;
    }

    /**
     * Returns the privacy
     *
     * @return int
     */
    public function getPrivacy()
    {
        return $this->privacy;
    }

    /**
     * Sets the privacy
     *
     * @param int $privacy
     * @return void
     */
    public function setPrivacy(int $privacy)
    {
        $this->privacy = $privacy;
    }

    /**
     * Returns the conditionsOfParticipation
     *
     * @return int
     */
    public function getConditionsOfParticipation()
    {
        return $this->conditionsOfParticipation;
    }

    /**
     * Sets the conditionsOfParticipation
     *
     * @param int $conditionsOfParticipation
     * @return void
     */
    public function setConditionsOfParticipation(int $conditionsOfParticipation)
    {
        $this->conditionsOfParticipation = $conditionsOfParticipation;
    }

    /**
     * Returns the isGroupWork
     *
     * @return int
     */
    public function getIsGroupWork()
    {
        return $this->isGroupWork;
    }

    /**
     * Sets the isGroupWork
     *
     * @param int|null $isGroupWork
     * @return void
     */
    public function setIsGroupWork(int $isGroupWork = null)
    {
        $this->isGroupWork = (int)$isGroupWork;
    }

    /**
     * Returns the groupWorkInsurance
     *
     * @return int
     */
    public function getGroupWorkInsurance()
    {
        return $this->groupWorkInsurance;
    }

    /**
     * Sets the groupWorkInsurance
     *
     * @param int $groupWorkInsurance
     * @return void
     */
    public function setGroupWorkInsurance(int $groupWorkInsurance = null)
    {
        $this->groupWorkInsurance = (int) $groupWorkInsurance;
    }

    /**
     * Returns the groupWorkAddPersons
     *
     * @return string
     */
    public function getGroupWorkAddPersons()
    {
        return $this->groupWorkAddPersons;
    }

    /**
     * Sets the groupWorkAddPersons
     *
     * @param string $groupWorkAddPersons
     * @return void
     */
    public function setGroupWorkAddPersons(string $groupWorkAddPersons)
    {
        $this->groupWorkAddPersons = $groupWorkAddPersons;
    }

    /**
     * Returns the userSubmittedAt
     *
     * @return int
     */
    public function getUserSubmittedAt()
    {
        return $this->userSubmittedAt;
    }

    /**
     * Sets the userSubmittedAt
     *
     * @param int $userSubmittedAt
     * @return void
     */
    public function setUserSubmittedAt(int $userSubmittedAt)
    {
        $this->userSubmittedAt = $userSubmittedAt;
    }

    /**
     * @return boolean
     */
    public function isAdminApproved()
    {
        return $this->adminApproved;
    }

    /**
     * @return boolean
     */
    public function getAdminApproved()
    {
        return $this->adminApproved;
    }

    /**
     * @param boolean $adminApproved
     */
    public function setAdminApproved($adminApproved)
    {
        $this->adminApproved = $adminApproved;
    }

    /**
     * Returns the adminApprovedAt
     *
     * @return int
     */
    public function getAdminApprovedAt()
    {
        return $this->adminApprovedAt;
    }

    /**
     * Sets the adminApprovedAt
     *
     * @param int $adminApprovedAt
     * @return void
     */
    public function setAdminApprovedAt(int $adminApprovedAt)
    {
        $this->adminApprovedAt = $adminApprovedAt;
    }

    /**
     * @return boolean
     */
    public function isAdminRefused()
    {
        return $this->adminRefused;
    }

    /**
     * @return boolean
     */
    public function getAdminRefused()
    {
        return $this->adminRefused;
    }

    /**
     * @param boolean $adminRefused
     */
    public function setAdminRefused($adminRefused)
    {
        $this->adminRefused = $adminRefused;
    }

    /**
     * Returns the adminRefusedAt
     *
     * @return int
     */
    public function getAdminRefusedAt()
    {
        return $this->adminRefusedAt;
    }

    /**
     * Sets the adminRefusedAt
     *
     * @param int $adminRefusedAt
     * @return void
     */
    public function setAdminRefusedAt(int $adminRefusedAt)
    {
        $this->adminRefusedAt = $adminRefusedAt;
    }

    /**
     * Returns the adminRefusedText
     *
     * @return string
     */
    public function getAdminRefusedText()
    {
        return $this->adminRefusedText;
    }

    /**
     * Sets the adminRefusedText
     *
     * @param string $adminRefusedText
     * @return void
     */
    public function setAdminRefusedText(string $adminRefusedText)
    {
        $this->adminRefusedText = $adminRefusedText;
    }

    /**
     * Returns the uniqueId
     *
     * @return string
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * Sets the uniqueId
     *
     * @param string $uniqueId
     * @return void
     */
    public function setUniqueId(string $uniqueId)
    {
        $this->uniqueId = $uniqueId;
    }

    /**
     * Returns the sector
     *
     * @return \RKW\RkwCompetition\Domain\Model\Sector
     */
    public function getSector()
    {
        return $this->sector;
    }

    /**
     * Sets the sector
     *
     * @param \RKW\RkwCompetition\Domain\Model\Sector $sector
     * @return void
     */
    public function setSector(\RKW\RkwCompetition\Domain\Model\Sector $sector = null)
    {
        $this->sector = $sector;
    }

    /**
     * Returns the upload
     *
     * @return \RKW\RkwCompetition\Domain\Model\Upload
     */
    public function getUpload()
    {
        return $this->upload;
    }

    /**
     * Sets the upload
     *
     * @param \RKW\RkwCompetition\Domain\Model\Upload $upload
     * @return void
     */
    public function setUpload(\RKW\RkwCompetition\Domain\Model\Upload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * Returns the frontendUser
     *
     * @return \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser
     */
    public function getFrontendUser()
    {
        return $this->frontendUser;
    }

    /**
     * Sets the frontendUser
     *
     * @param \Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser
     * @return void
     */
    public function setFrontendUser(\Madj2k\FeRegister\Domain\Model\FrontendUser $frontendUser)
    {
        $this->frontendUser = $frontendUser;
    }

    /**
     * Returns the competition
     *
     * @return \RKW\RkwCompetition\Domain\Model\Competition $competition
     */
    public function getCompetition()
    {
        return $this->competition;
    }

    /**
     * Sets the competition
     *
     * @param \RKW\RkwCompetition\Domain\Model\Competition $competition
     * @return void
     */
    public function setCompetition(\RKW\RkwCompetition\Domain\Model\Competition $competition)
    {
        $this->competition = $competition;
    }

    /**
     * Returns the adminApprovedBy
     *
     * @return \RKW\RkwCompetition\Domain\Model\BackendUser $adminApprovedBy
     */
    public function getAdminApprovedBy()
    {
        return $this->adminApprovedBy;
    }

    /**
     * Sets the adminApprovedBy
     *
     * @param \RKW\RkwCompetition\Domain\Model\BackendUser $adminApprovedBy
     * @return void
     */
    public function setAdminApprovedBy(\RKW\RkwCompetition\Domain\Model\BackendUser $adminApprovedBy)
    {
        $this->adminApprovedBy = $adminApprovedBy;
    }

    /**
     * Returns the adminRefusedBy
     *
     * @return \RKW\RkwCompetition\Domain\Model\BackendUser $adminRefusedBy
     */
    public function getAdminRefusedBy()
    {
        return $this->adminRefusedBy;
    }

    /**
     * Sets the adminRefusedBy
     *
     * @param \RKW\RkwCompetition\Domain\Model\BackendUser $adminRefusedBy
     * @return void
     */
    public function setAdminRefusedBy(\RKW\RkwCompetition\Domain\Model\BackendUser $adminRefusedBy)
    {
        $this->adminRefusedBy = $adminRefusedBy;
    }


}
