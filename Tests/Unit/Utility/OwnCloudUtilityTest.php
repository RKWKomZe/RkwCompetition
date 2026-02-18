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
     * @return void
     */
    public function testGetUserFolderSecretReturnsExpectedMd5(): void
    {
        /**
         * Scenario:
         *
         * Given a register with a frontend user (uid = 123)
         * And a competition with a defined creation date
         * When getUserFolderSecret is called
         * Then an MD5 hash is returned
         * And the hash equals md5(frontendUserUid + competitionCrdateTimestamp)
         */
        $frontendUser = $this->createMock(FrontendUser::class);
        $frontendUser->method('getUid')->willReturn(123);

        $competition = $this->createMock(Competition::class);
        $crdate = new \DateTime('2025-08-01 12:00:00');
        $competition->method('getCrdate')->willReturn($crdate);

        $register = $this->createMock(Register::class);
        $register->method('getFrontendUser')->willReturn($frontendUser);
        $register->method('getCompetition')->willReturn($competition);

        $expected = md5('123' . $crdate->getTimestamp());
        $this->assertSame($expected, OwnCloudUtility::getUserFolderSecret($register));
    }

    /**
     * @return void
     */
    public function testGetCompetitionFolderSecretReturnsExpectedMd5(): void
    {
        /**
         * Scenario:
         *
         * Given a competition with a uid (789)
         * And a defined creation date
         * When getCompetitionFolderSecret is called
         * Then an MD5 hash is returned
         * And the hash equals md5(competitionUid + competitionCrdateTimestamp)
         */
        $competition = $this->createMock(Competition::class);
        $competition->method('getUid')->willReturn(789);

        $crdate = new \DateTime('2025-08-01 15:00:00');
        $competition->method('getCrdate')->willReturn($crdate);

        $expected = md5('789' . $crdate->getTimestamp());
        $this->assertSame($expected, OwnCloudUtility::getCompetitionFolderSecret($competition));
    }

    /**
     * @return void
     */
    public function testSecretsAreMd5Hashes(): void
    {
        /**
         * Scenario:
         *
         * Given a competition with a valid uid and creation date
         * When getCompetitionFolderSecret is called
         * Then the returned value is a 32-character string
         * And the string matches the MD5 hexadecimal format (lowercase a-f, 0-9)
         */
        $competition = $this->createMock(Competition::class);
        $competition->method('getUid')->willReturn(1);
        $competition->method('getCrdate')->willReturn(new \DateTime('2025-01-01 00:00:00'));

        $hash = OwnCloudUtility::getCompetitionFolderSecret($competition);

        $this->assertSame(32, strlen($hash));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $hash);
    }
}
