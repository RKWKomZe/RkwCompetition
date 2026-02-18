<?php

namespace RKW\RkwCompetition\Tests\Unit\Utility;

use PHPUnit\Framework\TestCase;
use Random\RandomException;
use RKW\RkwCompetition\Domain\Model\Register;
use RKW\RkwCompetition\Utility\RegisterUtility;

class RegisterUtilityTest extends TestCase
{
    /**
     * @return void
     */
    public function testRegisterStatusReturnsNew(): void
    {
        /**
         * Scenario:
         *
         * Given a register with no approval, refusal or submission timestamps set
         * When registerStatus is called
         * Then the status STATUS_NEW (100) is returned
         */
        $register = $this->createMock(Register::class);
        $register->method('getAdminApprovedAt')->willReturn(null);
        $register->method('getAdminRefusedAt')->willReturn(null);
        $register->method('getUserSubmittedAt')->willReturn(null);

        $result = RegisterUtility::registerStatus($register);

        $this->assertEquals(RegisterUtility::STATUS_NEW, $result);
    }

    /**
     * @return void
     */
    public function testRegisterStatusReturnsApproved(): void
    {
        /**
         * Scenario:
         *
         * Given a register where adminApprovedAt is set
         * And adminRefusedAt and userSubmittedAt are not set
         * When registerStatus is called
         * Then the status STATUS_APPROVED (500) is returned
         */

        $register = $this->createMock(Register::class);
        $register->method('getAdminApprovedAt')->willReturn(new \DateTime());
        $register->method('getAdminRefusedAt')->willReturn(null);
        $register->method('getUserSubmittedAt')->willReturn(null);

        $result = RegisterUtility::registerStatus($register);

        $this->assertEquals(RegisterUtility::STATUS_APPROVED, $result);
    }

    /**
     * @return void
     */
    public function testRegisterStatusReturnsRefused(): void
    {
        /**
         * Scenario:
         *
         * Given a register where adminRefusedAt is set
         * And adminApprovedAt and userSubmittedAt are not set
         * When registerStatus is called
         * Then the status STATUS_REFUSED (200) is returned
         */
        $register = $this->createMock(Register::class);
        $register->method('getAdminApprovedAt')->willReturn(null);
        $register->method('getAdminRefusedAt')->willReturn(new \DateTime());
        $register->method('getUserSubmittedAt')->willReturn(null);

        $result = RegisterUtility::registerStatus($register);

        $this->assertEquals(RegisterUtility::STATUS_REFUSED, $result);
    }

    /**
     * @return void
     */
    public function testRegisterStatusReturnsSubmitted(): void
    {
        /**
         * Scenario:
         *
         * Given a register where userSubmittedAt is set
         * And adminApprovedAt and adminRefusedAt are not set
         * When registerStatus is called
         * Then the status STATUS_SUBMITTED (300) is returned
         */

        $register = $this->createMock(Register::class);
        $register->method('getAdminApprovedAt')->willReturn(null);
        $register->method('getAdminRefusedAt')->willReturn(null);
        $register->method('getUserSubmittedAt')->willReturn(new \DateTime());

        $result = RegisterUtility::registerStatus($register);

        $this->assertEquals(RegisterUtility::STATUS_SUBMITTED, $result);
    }

    /**
     * @return void
     * @throws RandomException
     */
    public function testCreateTokenGeneratesStringOfRequiredLength(): void
    {
        /**
         * Scenario:
         *
         * Given a desired token length in bytes
         * When createToken is called with that length
         * Then a string is returned
         * And the resulting string length equals length * 2 (because bin2hex doubles the byte length)
         */
        $length = 10;
        $token = RegisterUtility::createToken($length);
        $this->assertIsString($token);
        $this->assertEquals($length * 2, strlen($token)); // bin2hex doubles the length
    }

    /**
     * @return void
     * @throws RandomException
     */
    public function testCreateTokenGeneratesUniqueTokens(): void
    {
        /**
         * Scenario:
         *
         * Given two subsequent calls to createToken with the same length
         * When both tokens are generated
         * Then the two tokens are not equal
         * And each call produces a unique random value
         */
        $token1 = RegisterUtility::createToken(10);
        $token2 = RegisterUtility::createToken(10);
        $this->assertNotEquals($token1, $token2);
    }

    /**
     * @return void
     */
    public function testRegisterStatusApprovedHasPriority(): void
    {
        /**
         * Scenario:
         *
         * Given a register where adminApprovedAt, adminRefusedAt and userSubmittedAt are all set
         * When registerStatus is called
         * Then STATUS_APPROVED (500) is returned
         * And approved has priority over refused and submitted
         */
        $register = $this->createMock(Register::class);
        $register->method('getAdminApprovedAt')->willReturn(new \DateTime());
        $register->method('getAdminRefusedAt')->willReturn(new \DateTime());
        $register->method('getUserSubmittedAt')->willReturn(new \DateTime());

        $this->assertSame(RegisterUtility::STATUS_APPROVED, RegisterUtility::registerStatus($register));
    }
}
