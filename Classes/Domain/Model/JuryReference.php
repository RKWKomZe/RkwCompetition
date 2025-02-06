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
     * email
     *
     * @var string
     */
    protected $email = '';

    /**
     * inviteToken
     *
     * @var string
     */
    protected $inviteToken = '';

    /**
     * guestUser
     *
     * @var \Madj2k\FeRegister\Domain\Model\GuestUser
     */
    protected $guestUser = null;

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
     * Returns the inviteToken
     *
     * @return string $inviteToken
     */
    public function getInviteToken()
    {
        return $this->inviteToken;
    }

    /**
     * Sets the inviteToken
     *
     * @param string $inviteToken
     * @return void
     */
    public function setInviteToken(string $inviteToken)
    {
        $this->inviteToken = $inviteToken;
    }

    /**
     * Returns the guestUser
     *
     * @return \Madj2k\FeRegister\Domain\Model\GuestUser $guestUser
     */
    public function getGuestUser()
    {
        return $this->guestUser;
    }

    /**
     * Sets the guestUser
     *
     * @param \Madj2k\FeRegister\Domain\Model\GuestUser $guestUser
     * @return void
     */
    public function setGuestUser(\Madj2k\FeRegister\Domain\Model\GuestUser $guestUser)
    {
        $this->guestUser = $guestUser;
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
