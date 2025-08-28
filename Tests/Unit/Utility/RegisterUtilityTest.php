<?php

namespace RKW\RkwCompetition\Tests\Unit\Utility;

use PHPUnit\Framework\TestCase;
use Random\RandomException;
use RKW\RkwCompetition\Domain\Model\Register;
use RKW\RkwCompetition\Utility\RegisterUtility;

class RegisterUtilityTest extends TestCase
{
    /**
     * Test that registerStatus returns STATUS_NEW when no dates are set
     */
    public function testRegisterStatusReturnsNew(): void
    {
        $register = $this->createMock(Register::class);
        $register->method('getAdminApprovedAt')->willReturn(null);
        $register->method('getAdminRefusedAt')->willReturn(null);
        $register->method('getUserSubmittedAt')->willReturn(null);

        $result = RegisterUtility::registerStatus($register);

        $this->assertEquals(RegisterUtility::STATUS_NEW, $result);
    }

    /**
     * Test that registerStatus returns STATUS_APPROVED when adminApprovedAt is set
     */
    public function testRegisterStatusReturnsApproved(): void
    {
        $register = $this->createMock(Register::class);
        $register->method('getAdminApprovedAt')->willReturn(new \DateTime());
        $register->method('getAdminRefusedAt')->willReturn(null);
        $register->method('getUserSubmittedAt')->willReturn(null);

        $result = RegisterUtility::registerStatus($register);

        $this->assertEquals(RegisterUtility::STATUS_APPROVED, $result);
    }

    /**
     * Test that registerStatus returns STATUS_REFUSED when adminRefusedAt is set
     */
    public function testRegisterStatusReturnsRefused(): void
    {
        $register = $this->createMock(Register::class);
        $register->method('getAdminApprovedAt')->willReturn(null);
        $register->method('getAdminRefusedAt')->willReturn(new \DateTime());
        $register->method('getUserSubmittedAt')->willReturn(null);

        $result = RegisterUtility::registerStatus($register);

        $this->assertEquals(RegisterUtility::STATUS_REFUSED, $result);
    }

    /**
     * Test that registerStatus returns STATUS_SUBMITTED when userSubmittedAt is set
     */
    public function testRegisterStatusReturnsSubmitted(): void
    {
        $register = $this->createMock(Register::class);
        $register->method('getAdminApprovedAt')->willReturn(null);
        $register->method('getAdminRefusedAt')->willReturn(null);
        $register->method('getUserSubmittedAt')->willReturn(new \DateTime());

        $result = RegisterUtility::registerStatus($register);

        $this->assertEquals(RegisterUtility::STATUS_SUBMITTED, $result);
    }

    /**
     * Test createToken generates a string of the required length
     */
    public function testCreateTokenGeneratesStringOfRequiredLength(): void
    {
        $length = 10;
        $token = RegisterUtility::createToken($length);
        $this->assertIsString($token);
        $this->assertEquals($length * 2, strlen($token)); // bin2hex doubles the length
    }

    /**
     * Test createToken throws exception for invalid length
     */
    public function testCreateTokenThrowsExceptionForInvalidLength(): void
    {
        $this->expectException(RandomException::class);
        RegisterUtility::createToken(-1);
    }

    /**
     * Test createToken generates unique tokens
     */
    public function testCreateTokenGeneratesUniqueTokens(): void
    {
        $token1 = RegisterUtility::createToken(10);
        $token2 = RegisterUtility::createToken(10);
        $this->assertNotEquals($token1, $token2);
    }
}
