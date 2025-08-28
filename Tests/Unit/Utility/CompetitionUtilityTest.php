<?php

namespace RKW\RkwCompetition\Tests\Unit\Utility;

use PHPUnit\Framework\TestCase;
use RKW\RkwCompetition\Domain\Model\Competition;
use RKW\RkwCompetition\Utility\CompetitionUtility;

class CompetitionUtilityTest extends TestCase
{
    /**
     * Tests if hasRegTimeEnded returns true when the registration time has ended
     */
    public function testHasRegTimeEndedReturnsTrueWhenRegistrationTimeEnded(): void
    {
        $competition = $this->createMock(Competition::class);
        $pastDate = new \DateTime('-1 day');
        $competition->method('getRegisterEnd')->willReturn($pastDate);

        $result = CompetitionUtility::hasRegTimeEnded($competition);

        $this->assertTrue($result);
    }

    /**
     * Tests if hasRegTimeEnded returns false when the registration time has not ended
     */
    public function testHasRegTimeEndedReturnsFalseWhenRegistrationTimeNotEnded(): void
    {
        $competition = $this->createMock(Competition::class);
        $futureDate = new \DateTime('+1 day');
        $competition->method('getRegisterEnd')->willReturn($futureDate);

        $result = CompetitionUtility::hasRegTimeEnded($competition);

        $this->assertFalse($result);
    }


    /**
     * Tests if hasJuryAccessTimeEnded returns true when the jury access time has ended
     */
    public function testHasJuryAccessTimeEndedReturnsTrueWhenAccessTimeEnded(): void
    {
        $competition = $this->createMock(Competition::class);
        $pastDate = new \DateTime('-1 day');
        $competition->method('getJuryAccessEnd')->willReturn($pastDate);

        $result = CompetitionUtility::hasJuryAccessTimeEnded($competition);

        $this->assertTrue($result);
    }

    /**
     * Tests if hasJuryAccessTimeEnded returns false when the jury access time has not ended
     */
    public function testHasJuryAccessTimeEndedReturnsFalseWhenAccessTimeNotEnded(): void
    {
        $competition = $this->createMock(Competition::class);
        $futureDate = new \DateTime('+1 day');
        $competition->method('getJuryAccessEnd')->willReturn($futureDate);

        $result = CompetitionUtility::hasJuryAccessTimeEnded($competition);

        $this->assertFalse($result);
    }

}
