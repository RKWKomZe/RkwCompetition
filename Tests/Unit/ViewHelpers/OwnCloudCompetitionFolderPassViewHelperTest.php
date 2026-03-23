<?php


namespace RKW\RkwCompetition\Tests\Unit\ViewHelpers;

use PHPUnit\Framework\TestCase;
use RKW\RkwCompetition\Domain\Model\Competition;
use RKW\RkwCompetition\ViewHelpers\OwnCloudCompetitionFolderPassViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class OwnCloudCompetitionFolderPassViewHelperTest extends TestCase
{
    /**
     * @test
     */
    public function renderStaticThrowsTypeErrorWhenCompetitionIsPassed(): void
    {
        /**
         * Scenario:
         *
         * Given a valid Competition object is provided as "competition" argument
         * When renderStatic() is executed
         * Then OwnCloudUtility::getUserFolderSecret() is called with a Competition object
         * Then a TypeError is thrown because getUserFolderSecret() expects a Register instance
         *
         * This verifies the current implementation is type-incompatible and cannot succeed
         * as long as it calls getUserFolderSecret() with Competition.
         */
        $competition = $this->createMock(Competition::class);

        $arguments = [
            'competition' => $competition,
        ];

        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $this->expectException(\TypeError::class);

        OwnCloudCompetitionFolderPassViewHelper::renderStatic(
            $arguments,
            static function () {
            },
            $renderingContext
        );
    }

    /**
     * @test
     */
    public function renderStaticThrowsRuntimeExceptionWhenCompetitionArgumentIsMissing(): void
    {
        /**
         * Scenario:
         *
         * Given no "competition" argument is provided
         * When renderStatic() is executed
         * Then accessing $arguments['competition'] triggers a PHP warning (undefined array key)
         * Then this warning is converted into a RuntimeException by the test
         *
         * This verifies that the ViewHelper does not validate required arguments
         * and will fail when mandatory input is missing.
         */
        $previousHandler = set_error_handler(
            static function (int $severity, string $message): bool {
                throw new \RuntimeException($message);
            }
        );

        try {
            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessageMatches('/competition/i');

            $renderingContext = $this->createMock(RenderingContextInterface::class);

            OwnCloudCompetitionFolderPassViewHelper::renderStatic(
                [],
                static function () {
                },
                $renderingContext
            );
        } finally {
            restore_error_handler();
        }
    }

    /**
     * @test
     */
    public function renderStaticThrowsTypeErrorWhenCompetitionIsNull(): void
    {
        /**
         * Scenario:
         *
         * Given the "competition" argument is explicitly set to null
         * When renderStatic() is executed
         * Then OwnCloudUtility::getUserFolderSecret() is called with null
         * Then a TypeError is thrown because getUserFolderSecret() expects a Register instance
         *
         * This verifies that even a present-but-null argument fails hard
         * due to the strict parameter type of the utility method.
         */
        $arguments = [
            'competition' => null,
        ];

        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $this->expectException(\TypeError::class);

        OwnCloudCompetitionFolderPassViewHelper::renderStatic(
            $arguments,
            static function () {
            },
            $renderingContext
        );
    }
}
