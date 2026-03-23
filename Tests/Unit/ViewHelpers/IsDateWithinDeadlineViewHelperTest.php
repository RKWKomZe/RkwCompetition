<?php

namespace RKW\RkwCompetition\Tests\Unit\ViewHelpers;

use DateTime;
use PHPUnit\Framework\TestCase;
use RKW\RkwCompetition\ViewHelpers\IsDateWithinDeadlineViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class IsDateWithinDeadlineViewHelperTest extends TestCase
{
    /**
     * @test
     */
    public function renderStaticReturnsTrueForFutureDate()
    {
        /**
         * Scenario:
         *
         * Given a DateTime object that lies in the future (e.g. +1 day from now)
         * When the ViewHelper renderStatic() method is called with this date
         * Then the timestamp of the given date is greater than the current time()
         * Then the ViewHelper returns true
         *
         * This verifies that the ViewHelper correctly identifies dates
         * that are still within the deadline (i.e. not yet expired).
         */
        $futureDate = new DateTime('+1 day');
        $arguments = ['date' => $futureDate];
        $renderingContext = $this->createMock(RenderingContextInterface::class);
        $closure = function () {
        };

        $result = IsDateWithinDeadlineViewHelper::renderStatic($arguments, $closure, $renderingContext);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function renderStaticReturnsFalseForPastDate()
    {
        /**
         * Scenario:
         *
         * Given a DateTime object that lies in the past (e.g. -1 day from now)
         * When the ViewHelper renderStatic() method is called with this date
         * Then the timestamp of the given date is less than or equal to the current time()
         * Then the ViewHelper returns false
         *
         * This verifies that the ViewHelper correctly identifies
         * deadlines that have already expired.
         */
        $pastDate = new DateTime('-1 day');
        $arguments = ['date' => $pastDate];
        $renderingContext = $this->createMock(RenderingContextInterface::class);
        $closure = function () {
        };

        $result = IsDateWithinDeadlineViewHelper::renderStatic($arguments, $closure, $renderingContext);

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function renderStaticReturnsFalseForPresentDate()
    {
        /**
         * Scenario:
         *
         * Given a DateTime object representing the current moment
         * When the ViewHelper renderStatic() method is called with this date
         * Then the timestamp of the given date is not greater than the current time()
         * Then the ViewHelper returns false
         *
         * This verifies that a date equal to "now" is not considered
         * to be within a future deadline.
         */
        $currentDate = new DateTime();
        $arguments = ['date' => $currentDate];
        $renderingContext = $this->createMock(RenderingContextInterface::class);
        $closure = function () {
        };

        $result = IsDateWithinDeadlineViewHelper::renderStatic($arguments, $closure, $renderingContext);

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function renderStaticThrowsExceptionForInvalidDateArgument()
    {
        /**
         * Scenario:
         *
         * Given an invalid argument for "date" (e.g. a string instead of a DateTime object)
         * When the ViewHelper renderStatic() method is called
         * Then an Error is thrown because getTimestamp() is called on a non-object
         *
         * This verifies that the ViewHelper currently does not perform
         * explicit type validation and will fail hard if an invalid type is passed.
         */
        $this->expectException(\Error::class);

        $arguments = ['date' => 'invalid-date'];
        $renderingContext = $this->createMock(RenderingContextInterface::class);
        $closure = static function () {};

        IsDateWithinDeadlineViewHelper::renderStatic($arguments, $closure, $renderingContext);
    }

    /**
     * @test
     */
    public function renderStaticReturnsFalseWhenDateIsExactlyNow(): void
    {
        /**
         * Scenario:
         *
         * Given a DateTime instance that is exactly equal to the current timestamp
         * When the ViewHelper renderStatic() method is executed
         * Then the method returns false because the timestamp is not greater than "now"
         *
         * This ensures that the comparison is strictly ">" and not ">=",
         * meaning a deadline that equals the current time is already considered expired.
         */
        $now = (new \DateTimeImmutable())->setTimestamp(time());
        $arguments = ['date' => $now];
        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $result = IsDateWithinDeadlineViewHelper::renderStatic($arguments, static function () {}, $renderingContext);

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function renderStaticThrowsErrorWhenDateArgumentIsMissing(): void
    {
        /**
         * Scenario:
         *
         * Given no "date" argument is provided to the ViewHelper
         * When renderStatic() is called without the required DateTime argument
         * Then an Error is thrown because the method attempts to access getTimestamp() on a missing value
         *
         * This verifies that the ViewHelper does not silently accept invalid input
         * and fails fast when mandatory arguments are missing.
         */
        $this->expectException(\Error::class);

        $renderingContext = $this->createMock(RenderingContextInterface::class);

        IsDateWithinDeadlineViewHelper::renderStatic([], static function () {}, $renderingContext);
    }

    /**
     * @test
     */
    public function renderStaticReturnsTrueForFutureDateTimeImmutable(): void
    {
        /**
         * Scenario:
         *
         * Given a DateTimeImmutable instance with a timestamp in the future
         * When renderStatic() is called with this DateTimeImmutable object
         * Then the method returns true
         *
         * This verifies that the ViewHelper correctly handles DateTimeImmutable
         * objects and treats future timestamps as being within the deadline.
         */
        $future = (new \DateTimeImmutable())->modify('+1 day');
        $arguments = ['date' => $future];
        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $this->assertTrue(
            IsDateWithinDeadlineViewHelper::renderStatic($arguments, static function () {}, $renderingContext)
        );
    }

}
