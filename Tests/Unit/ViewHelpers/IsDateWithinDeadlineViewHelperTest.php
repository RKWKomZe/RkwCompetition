<?php

namespace RKW\RkwCompetition\Tests\Unit\ViewHelpers;

use DateTime;
use PHPUnit\Framework\TestCase;
use RKW\RkwCompetition\ViewHelpers\IsDateWithinDeadlineViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class IsDateWithinDeadlineViewHelperTest extends TestCase
{
    /**
     * Test case for valid future date
     */
    public function testRenderStaticReturnsTrueForFutureDate()
    {
        $futureDate = new DateTime('+1 day');
        $arguments = ['date' => $futureDate];
        $renderingContext = $this->createMock(RenderingContextInterface::class);
        $closure = function () {
        };

        $result = IsDateWithinDeadlineViewHelper::renderStatic($arguments, $closure, $renderingContext);

        $this->assertTrue($result);
    }

    /**
     * Test case for a past date
     */
    public function testRenderStaticReturnsFalseForPastDate()
    {
        $pastDate = new DateTime('-1 day');
        $arguments = ['date' => $pastDate];
        $renderingContext = $this->createMock(RenderingContextInterface::class);
        $closure = function () {
        };

        $result = IsDateWithinDeadlineViewHelper::renderStatic($arguments, $closure, $renderingContext);

        $this->assertFalse($result);
    }

    /**
     * Test case for the current timestamp (not yet expired)
     */
    public function testRenderStaticReturnsFalseForPresentDate()
    {
        $currentDate = new DateTime();
        $arguments = ['date' => $currentDate];
        $renderingContext = $this->createMock(RenderingContextInterface::class);
        $closure = function () {
        };

        $result = IsDateWithinDeadlineViewHelper::renderStatic($arguments, $closure, $renderingContext);

        $this->assertFalse($result);
    }

    /**
     * Test case for invalid date argument (throws error)
     */
    public function testRenderStaticThrowsExceptionForInvalidDateArgument()
    {
        $this->expectException(\TypeError::class);

        $arguments = ['date' => 'invalid-date'];
        $renderingContext = $this->createMock(RenderingContextInterface::class);
        $closure = function () {
        };

        IsDateWithinDeadlineViewHelper::renderStatic($arguments, $closure, $renderingContext);
    }
}
