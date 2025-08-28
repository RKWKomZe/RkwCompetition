<?php

namespace RKW\RkwCompetition\Tests\Unit\Utility;

use Madj2k\FeRegister\Domain\Model\FrontendUser;
use PHPUnit\Framework\TestCase;
use RKW\RkwCompetition\Domain\Model\Competition;
use RKW\RkwCompetition\Domain\Model\Register;
use RKW\RkwCompetition\Utility\OwnCloudUtility;

/**
 * Class OwnCloudUtilityTest
 *
 * Unit test for OwnCloudUtility::getUserFolderSecret method.
 */
class OwnCloudUtilityTest extends TestCase
{
    /**
     * Tests that getUserFolderSecret returns a valid hash.
     */
    public function testGetUserFolderSecretReturnsValidHash(): void
    {
        // Arrange
        $frontendUser = $this->createMock(FrontendUser::class);
        $frontendUser->method('getUid')->willReturn(123);

        $competition = $this->createMock(Competition::class);
        $crdate = new \DateTime('2025-08-01 12:00:00');
        $competition->method('getCrdate')->willReturn($crdate);

        $register = $this->createMock(Register::class);
        $register->method('getFrontendUser')->willReturn($frontendUser);
        $register->method('getCompetition')->willReturn($competition);

        // Act
        $result = OwnCloudUtility::getUserFolderSecret($register);

        // Assert
        $expectedHash = md5(123 . $crdate->getTimestamp());
        $this->assertEquals($expectedHash, $result);
    }

    /**
     * Tests that getUserFolderSecret handles missing frontend user gracefully.
     */
    public function testGetUserFolderSecretHandlesMissingFrontendUser(): void
    {
        // Arrange
        $competition = $this->createMock(Competition::class);
        $crdate = new \DateTime('2025-08-01 12:00:00');
        $competition->method('getCrdate')->willReturn($crdate);

        $register = $this->createMock(Register::class);
        $register->method('getFrontendUser')->willReturn(null);
        $register->method('getCompetition')->willReturn($competition);

        // Assert
        $this->expectException(\TypeError::class);

        // Act
        OwnCloudUtility::getUserFolderSecret($register);
    }

    /**
     * Tests that getUserFolderSecret handles missing competition gracefully.
     */
    public function testGetUserFolderSecretHandlesMissingCompetition(): void
    {
        // Arrange
        $frontendUser = $this->createMock(FrontendUser::class);
        $frontendUser->method('getUid')->willReturn(456);

        $register = $this->createMock(Register::class);
        $register->method('getFrontendUser')->willReturn($frontendUser);
        $register->method('getCompetition')->willReturn(null);

        // Assert
        $this->expectException(\TypeError::class);

        // Act
        OwnCloudUtility::getUserFolderSecret($register);
    }


    /**
     * Tests that getCompetitionFolderSecret returns a valid hash.
     */
    public function testGetCompetitionFolderSecretReturnsValidHash(): void
    {
        // Arrange
        $competition = $this->createMock(Competition::class);
        $competition->method('getUid')->willReturn(789);
        $crdate = new \DateTime('2025-08-01 15:00:00');
        $competition->method('getCrdate')->willReturn($crdate);

        // Act
        $result = OwnCloudUtility::getCompetitionFolderSecret($competition);

        // Assert
        $expectedHash = md5(789 . $crdate->getTimestamp());
        $this->assertEquals($expectedHash, $result);
    }

    /**
     * Tests that getCompetitionFolderSecret handles missing competition creation date gracefully.
     */
    public function testGetCompetitionFolderSecretHandlesMissingCrdate(): void
    {
        // Arrange
        $competition = $this->createMock(Competition::class);
        $competition->method('getUid')->willReturn(789);
        $competition->method('getCrdate')->willReturn(null);

        // Assert
        $this->expectException(\TypeError::class);

        // Act
        OwnCloudUtility::getCompetitionFolderSecret($competition);
    }

    /**
     * Tests that getCompetitionFolderSecret handles missing competition UID gracefully.
     */
    public function testGetCompetitionFolderSecretHandlesMissingUid(): void
    {
        // Arrange
        $competition = $this->createMock(Competition::class);
        $competition->method('getUid')->willReturn(null);
        $crdate = new \DateTime('2025-08-01 15:00:00');
        $competition->method('getCrdate')->willReturn($crdate);

        // Assert
        $this->expectException(\TypeError::class);

        // Act
        OwnCloudUtility::getCompetitionFolderSecret($competition);
    }
}
