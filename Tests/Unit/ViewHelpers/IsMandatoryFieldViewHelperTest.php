<?php

namespace RKW\RkwCompetition\Tests\Unit\ViewHelpers;

use PHPUnit\Framework\TestCase;
use RKW\RkwCompetition\ViewHelpers\IsMandatoryFieldViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class IsMandatoryFieldViewHelperTest extends TestCase
{
    /**
     * @test
     */
    public function renderStaticReturnsFalseWhenFieldIsMandatory(): void
    {
        /**
         * Scenario:
         *
         * Given a field name that is listed as mandatory
         * Given a comma separated mandatoryFields string containing that field name
         * When the ViewHelper renderStatic() method is called
         * Then the field name is found in the parsed mandatoryFields array
         * Then the ViewHelper returns false (meaning: field IS mandatory)
         *
         * This verifies that the ViewHelper detects mandatory fields correctly.
         */
        $arguments = [
            'fieldName' => 'email',
            'mandatoryFields' => 'email, firstname, lastname'
        ];

        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $result = IsMandatoryFieldViewHelper::renderStatic($arguments, static function () {}, $renderingContext);

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function renderStaticReturnsTrueWhenFieldIsNotMandatory(): void
    {
        /**
         * Scenario:
         *
         * Given a field name that is NOT listed as mandatory
         * Given a comma separated mandatoryFields string not containing that field name
         * When the ViewHelper renderStatic() method is called
         * Then the field name is not found in the parsed mandatoryFields array
         * Then the ViewHelper returns true (meaning: field is NOT mandatory)
         *
         * This verifies that the ViewHelper returns true for optional fields.
         */
        $arguments = [
            'fieldName' => 'company',
            'mandatoryFields' => 'email, firstname, lastname'
        ];

        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $result = IsMandatoryFieldViewHelper::renderStatic($arguments, static function () {}, $renderingContext);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function renderStaticTrimsWhitespaceInMandatoryFieldsList(): void
    {
        /**
         * Scenario:
         *
         * Given a field name that is mandatory
         * Given a mandatoryFields string where the entries contain leading/trailing whitespace
         * When the ViewHelper renderStatic() method is called
         * Then the mandatory fields are split and trimmed
         * Then the field name is still detected as mandatory
         * Then the ViewHelper returns false
         *
         * This verifies that whitespace in TypoScript lists does not break matching.
         */
        $arguments = [
            'fieldName' => 'email',
            'mandatoryFields' => ' email , firstname ,  lastname '
        ];

        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $result = IsMandatoryFieldViewHelper::renderStatic($arguments, static function () {}, $renderingContext);

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function renderStaticIsCaseSensitive(): void
    {
        /**
         * Scenario:
         *
         * Given a field name with a different case than the mandatory list entry
         * Given mandatoryFields contains "email" but fieldName is "Email"
         * When the ViewHelper renderStatic() method is called
         * Then the field name is not found in the parsed mandatoryFields array (case-sensitive match)
         * Then the ViewHelper returns true
         *
         * This documents current behavior: matching is case-sensitive.
         * If you later want case-insensitive behavior, this test will need to be adjusted.
         */
        $arguments = [
            'fieldName' => 'Email',
            'mandatoryFields' => 'email, firstname, lastname'
        ];

        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $result = IsMandatoryFieldViewHelper::renderStatic($arguments, static function () {}, $renderingContext);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function renderStaticReturnsTrueWhenMandatoryFieldsIsEmptyString(): void
    {
        /**
         * Scenario:
         *
         * Given a field name
         * Given an empty mandatoryFields string (no mandatory fields configured)
         * When the ViewHelper renderStatic() method is called
         * Then the parsed mandatoryFields array does not contain the field name
         * Then the ViewHelper returns true
         *
         * This verifies that the ViewHelper treats all fields as optional
         * when no mandatory fields are configured.
         */
        $arguments = [
            'fieldName' => 'email',
            'mandatoryFields' => ''
        ];

        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $result = IsMandatoryFieldViewHelper::renderStatic($arguments, static function () {}, $renderingContext);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function renderStaticThrowsErrorWhenFieldNameIsMissing(): void
    {
        /**
         * Scenario:
         *
         * Given no "fieldName" argument is provided to the ViewHelper
         * When renderStatic() is called without the required fieldName argument
         * Then an Error is thrown because the method attempts to read a missing array key
         *
         * This verifies that the ViewHelper currently requires the argument
         * and fails fast when it is not present.
         */
        $previousHandler = set_error_handler(
            static function (int $severity, string $message): bool {
                throw new \RuntimeException($message);
            }
        );

        try {
            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessageMatches('/fieldName/i');

            $arguments = [
                'mandatoryFields' => 'email, firstname'
            ];

            $renderingContext = $this->createMock(\TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface::class);

            \RKW\RkwCompetition\ViewHelpers\IsMandatoryFieldViewHelper::renderStatic(
                $arguments,
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
    /**
     * @test
     */
    public function renderStaticThrowsExceptionWhenMandatoryFieldsIsMissing(): void
    {
        /**
         * Scenario:
         *
         * Given a valid "fieldName" argument is provided
         * Given no "mandatoryFields" argument is provided
         * When renderStatic() is executed
         * Then a PHP warning occurs because explode() receives a missing value
         * Then this warning is converted into a RuntimeException
         *
         * This verifies that the ViewHelper currently depends on the
         * mandatoryFields argument and fails when it is not present.
         */

        $previousHandler = set_error_handler(
            static function (int $severity, string $message): bool {
                throw new \RuntimeException($message);
            }
        );

        try {
            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessageMatches('/mandatoryFields/i');

            $arguments = [
                'fieldName' => 'email'
            ];

            $renderingContext = $this->createMock(RenderingContextInterface::class);

            IsMandatoryFieldViewHelper::renderStatic(
                $arguments,
                static function () {},
                $renderingContext
            );

        } finally {
            restore_error_handler();
        }
    }
}
