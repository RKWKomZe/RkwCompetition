<?php

namespace RKW\RkwCompetition\Tests\Unit\ViewHelpers;

use PHPUnit\Framework\TestCase;
use RKW\RkwCompetition\Domain\Model\Register;
use RKW\RkwCompetition\ViewHelpers\OwnCloudUserFolderPassViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class OwnCloudUserFolderPassViewHelperTest extends TestCase
{
    /**
     * @test
     */
    public function renderStaticReturnsSecretForValidRegister(): void
    {
        /**
         * Scenario:
         *
         * Given a valid Register object is provided as "register" argument
         * And the underlying Register/FrontendUser/Competition values are set so that OwnCloudUtility can compute a secret
         * When renderStatic() is executed
         * Then OwnCloudUtility::getUserFolderSecret() is called with this Register object
         * Then a 32 character MD5 hash string is returned
         *
         * This verifies that the ViewHelper correctly delegates to OwnCloudUtility
         * and returns the computed user folder secret.
         */
        $feUser = $this->createMock(\Madj2k\FeRegister\Domain\Model\FrontendUser::class);
        $feUser->method('getUid')->willReturn(123);

        $competition = $this->createMock(\RKW\RkwCompetition\Domain\Model\Competition::class);
        $crdate = new \DateTime('2025-08-01 12:00:00');
        $competition->method('getCrdate')->willReturn($crdate);

        $register = $this->createMock(\RKW\RkwCompetition\Domain\Model\Register::class);
        $register->method('getFrontendUser')->willReturn($feUser);
        $register->method('getCompetition')->willReturn($competition);

        $arguments = ['register' => $register];
        $renderingContext = $this->createMock(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface::class);

        $result = \RKW\RkwCompetition\ViewHelpers\OwnCloudUserFolderPassViewHelper::renderStatic(
            $arguments,
            static function () {},
            $renderingContext
        );

        $expected = md5('123' . $crdate->getTimestamp());
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     */
    public function renderStaticThrowsRuntimeExceptionWhenRegisterArgumentIsMissing(): void
    {
        /**
         * Scenario:
         *
         * Given no "register" argument is provided
         * When renderStatic() is executed
         * Then accessing $arguments['register'] triggers a PHP warning (undefined array key)
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
            $this->expectExceptionMessageMatches('/register/i');

            $renderingContext = $this->createMock(RenderingContextInterface::class);

            OwnCloudUserFolderPassViewHelper::renderStatic(
                [],
                static function () {},
                $renderingContext
            );
        } finally {
            restore_error_handler();
        }
    }

    /**
     * @test
     */
    public function renderStaticThrowsTypeErrorWhenRegisterIsNull(): void
    {
        /**
         * Scenario:
         *
         * Given the "register" argument is explicitly set to null
         * When renderStatic() is executed
         * Then OwnCloudUtility::getUserFolderSecret() is called with null
         * Then a TypeError is thrown because getUserFolderSecret() expects a Register instance
         *
         * This verifies that a present-but-null argument fails hard
         * due to the strict parameter type of the utility method.
         */
        $arguments = [
            'register' => null,
        ];

        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $this->expectException(\TypeError::class);

        OwnCloudUserFolderPassViewHelper::renderStatic(
            $arguments,
            static function () {},
            $renderingContext
        );
    }

    /**
     * @test
     */
    public function renderStaticThrowsTypeErrorWhenRegisterHasWrongType(): void
    {
        /**
         * Scenario:
         *
         * Given the "register" argument is provided but is not a Register object (e.g. a string)
         * When renderStatic() is executed
         * Then OwnCloudUtility::getUserFolderSecret() is called with an invalid type
         * Then a TypeError is thrown due to the strict parameter type
         *
         * This verifies that invalid input types are not tolerated and fail fast.
         */
        $arguments = [
            'register' => 'not-a-register',
        ];

        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $this->expectException(\TypeError::class);

        OwnCloudUserFolderPassViewHelper::renderStatic(
            $arguments,
            static function () {},
            $renderingContext
        );
    }
}
