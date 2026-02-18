<?php

namespace RKW\RkwCompetition\Tests\Unit\Utility;

use PHPUnit\Framework\TestCase;
use RKW\RkwCompetition\Domain\Model\Competition;
use RKW\RkwCompetition\Utility\CompetitionUtility;

class CompetitionUtilityTest extends TestCase
{
    /**
     * @return void
     */
    public function testHasRegTimeEndedReturnsTrueWhenRegistrationTimeEnded(): void
    {
        /**
         * Scenario:
         *
         * Given a competition
         * Given the registration end date lies in the past
         * When hasRegTimeEnded is called
         * Then the method returns true
         */

        $competition = $this->createMock(Competition::class);
        $pastDate = new \DateTime('-1 day');
        $competition->method('getRegisterEnd')->willReturn($pastDate);

        $result = CompetitionUtility::hasRegTimeEnded($competition);

        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testHasRegTimeEndedReturnsFalseWhenRegistrationTimeNotEnded(): void
    {
        /**
         * Scenario:
         *
         * Given a competition
         * Given the registration end date lies in the future
         * When hasRegTimeEnded is called
         * Then the method returns false
         */

        $competition = $this->createMock(Competition::class);
        $futureDate = new \DateTime('+1 day');
        $competition->method('getRegisterEnd')->willReturn($futureDate);

        $result = CompetitionUtility::hasRegTimeEnded($competition);

        $this->assertFalse($result);
    }


    /**
     * @return void
     */
    public function testHasJuryAccessTimeEndedReturnsTrueWhenAccessTimeEnded(): void
    {
        /**
         * Scenario:
         *
         * Given a competition
         * Given the jury access end date lies in the past
         * When hasJuryAccessTimeEnded is called
         * Then the method returns true
         */

        $competition = $this->createMock(Competition::class);
        $pastDate = new \DateTime('-1 day');
        $competition->method('getJuryAccessEnd')->willReturn($pastDate);

        $result = CompetitionUtility::hasJuryAccessTimeEnded($competition);

        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function testHasJuryAccessTimeEndedReturnsFalseWhenAccessTimeNotEnded(): void
    {
        /**
         * Scenario:
         *
         * Given a competition
         * Given the jury access end date lies in the future
         * When hasJuryAccessTimeEnded is called
         * Then the method returns false
         */

        $competition = $this->createMock(Competition::class);
        $futureDate = new \DateTime('+1 day');
        $competition->method('getJuryAccessEnd')->willReturn($futureDate);

        $result = CompetitionUtility::hasJuryAccessTimeEnded($competition);

        $this->assertFalse($result);
    }

    /**
     * @return void
     */
    public function testHasRegTimeEndedReturnsFalseWhenRegisterEndIsNow(): void
    {
        /**
         * Scenario:
         *
         * Given a competition
         * Given the registration end date is exactly the current timestamp
         * When hasRegTimeEnded is called
         * Then the method returns false
         */

        $competition = $this->createMock(Competition::class);
        $now = (new \DateTime())->setTimestamp(time());
        $competition->method('getRegisterEnd')->willReturn($now);

        $this->assertFalse(CompetitionUtility::hasRegTimeEnded($competition));
    }

}
