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
 * Upload
 */
class JuryReference extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * invitationMailTstamp
     *
     * @var integer
     */
    protected int $invitationMailTstamp = 0;

    /**
     * consentedAt
     *
     * @var integer
     */
    protected int $consentedAt = 0;

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
     * @return int
     */
    public function getInvitationMailTstamp(): int
    {
        return $this->invitationMailTstamp;
    }

    /**
     * @param int $invitationMailTstamp
     * @return void
     */
    public function setInvitationMailTstamp(int $invitationMailTstamp): void
    {
        $this->invitationMailTstamp = $invitationMailTstamp;
    }

    /**
     * @return int
     */
    public function getConsentedAt(): int
    {
        return $this->consentedAt;
    }

    /**
     * @param int $consentedAt
     * @return void
     */
    public function setConsentedAt(int $consentedAt): void
    {
        $this->consentedAt = $consentedAt;
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
}
