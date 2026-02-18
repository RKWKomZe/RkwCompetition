<?php

namespace RKW\RkwCompetition\Tests\Unit\ViewHelpers;

use PHPUnit\Framework\TestCase;
use RKW\RkwCompetition\Domain\Model\Register;
use RKW\RkwCompetition\Utility\RegisterUtility;
use RKW\RkwCompetition\ViewHelpers\RegisterStatusViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class RegisterStatusViewHelperTest extends TestCase
{
    /**
     * @test
     */
    public function renderStaticReturnsStatusNewWhenNoDatesAreSet(): void
    {
        /**
         * Scenario:
         *
         * Given a Register object where adminApprovedAt/adminRefusedAt/userSubmittedAt are all null
         * When the ViewHelper renderStatic() method is called with this Register
         * Then RegisterUtility::registerStatus() returns STATUS_NEW
         * Then the ViewHelper returns the same STATUS_NEW value
         *
         * This verifies that the ViewHelper correctly delegates the status calculation
         * and returns the default "new" status when no timestamps are present.
         */
        $register = $this->createMock(Register::class);
        $register->method('getAdminApprovedAt')->willReturn(null);
        $register->method('getAdminRefusedAt')->willReturn(null);
        $register->method('getUserSubmittedAt')->willReturn(null);

        $arguments = ['register' => $register];
        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $result = RegisterStatusViewHelper::renderStatic($arguments, static function () {}, $renderingContext);

        $this->assertSame(RegisterUtility::STATUS_NEW, $result);
    }

    /**
     * @test
     */
    public function renderStaticReturnsStatusApprovedWhenAdminApprovedAtIsSet(): void
    {
        /**
         * Scenario:
         *
         * Given a Register object with adminApprovedAt set (approved)
         * And adminRefusedAt and userSubmittedAt are irrelevant (may be null)
         * When renderStatic() is called
         * Then RegisterUtility::registerStatus() resolves STATUS_APPROVED
         * Then the ViewHelper returns STATUS_APPROVED
         *
         * This verifies that the ViewHelper returns the "approved" status as soon as
         * the admin approval timestamp is present.
         */
        $register = $this->createMock(Register::class);
        $register->method('getAdminApprovedAt')->willReturn(new \DateTime());
        $register->method('getAdminRefusedAt')->willReturn(null);
        $register->method('getUserSubmittedAt')->willReturn(null);

        $arguments = ['register' => $register];
        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $result = RegisterStatusViewHelper::renderStatic($arguments, static function () {}, $renderingContext);

        $this->assertSame(RegisterUtility::STATUS_APPROVED, $result);
    }

    /**
     * @test
     */
    public function renderStaticReturnsStatusRefusedWhenAdminRefusedAtIsSet(): void
    {
        /**
         * Scenario:
         *
         * Given a Register object with adminRefusedAt set (refused)
         * And adminApprovedAt is null
         * When renderStatic() is called
         * Then RegisterUtility::registerStatus() resolves STATUS_REFUSED
         * Then the ViewHelper returns STATUS_REFUSED
         *
         * This verifies that the ViewHelper returns the "refused" status when the
         * refusal timestamp exists and no approval timestamp exists.
         */
        $register = $this->createMock(Register::class);
        $register->method('getAdminApprovedAt')->willReturn(null);
        $register->method('getAdminRefusedAt')->willReturn(new \DateTime());
        $register->method('getUserSubmittedAt')->willReturn(null);

        $arguments = ['register' => $register];
        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $result = RegisterStatusViewHelper::renderStatic($arguments, static function () {}, $renderingContext);

        $this->assertSame(RegisterUtility::STATUS_REFUSED, $result);
    }

    /**
     * @test
     */
    public function renderStaticReturnsStatusSubmittedWhenUserSubmittedAtIsSet(): void
    {
        /**
         * Scenario:
         *
         * Given a Register object with userSubmittedAt set (submitted)
         * And adminApprovedAt and adminRefusedAt are null
         * When renderStatic() is called
         * Then RegisterUtility::registerStatus() resolves STATUS_SUBMITTED
         * Then the ViewHelper returns STATUS_SUBMITTED
         *
         * This verifies that the ViewHelper returns the "submitted" status when the
         * user submission timestamp exists and no admin decision exists yet.
         */
        $register = $this->createMock(Register::class);
        $register->method('getAdminApprovedAt')->willReturn(null);
        $register->method('getAdminRefusedAt')->willReturn(null);
        $register->method('getUserSubmittedAt')->willReturn(new \DateTime());

        $arguments = ['register' => $register];
        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $result = RegisterStatusViewHelper::renderStatic($arguments, static function () {}, $renderingContext);

        $this->assertSame(RegisterUtility::STATUS_SUBMITTED, $result);
    }

    /**
     * @test
     */
    public function renderStaticReturnsApprovedWhenAllDatesAreSetBecauseApprovedHasPriority(): void
    {
        /**
         * Scenario:
         *
         * Given a Register object where adminApprovedAt, adminRefusedAt and userSubmittedAt are all set
         * When renderStatic() is called
         * Then RegisterUtility::registerStatus() resolves STATUS_APPROVED because approval has priority
         * Then the ViewHelper returns STATUS_APPROVED
         *
         * This verifies that the ViewHelper reflects the priority logic from RegisterUtility:
         * "approved" wins over "refused" and "submitted".
         */
        $register = $this->createMock(Register::class);
        $register->method('getAdminApprovedAt')->willReturn(new \DateTime());
        $register->method('getAdminRefusedAt')->willReturn(new \DateTime());
        $register->method('getUserSubmittedAt')->willReturn(new \DateTime());

        $arguments = ['register' => $register];
        $renderingContext = $this->createMock(RenderingContextInterface::class);

        $result = RegisterStatusViewHelper::renderStatic($arguments, static function () {}, $renderingContext);

        $this->assertSame(RegisterUtility::STATUS_APPROVED, $result);
    }

    /**
     * @test
     */
    public function renderStaticThrowsErrorWhenRegisterArgumentIsMissing(): void
    {
        /**
         * Scenario:
         *
         * Given no "register" argument is provided to the ViewHelper
         * When renderStatic() is called without the required Register object
         * Then an Error is thrown because the method accesses $arguments['register']
         *
         * This verifies that the ViewHelper currently requires the argument and fails fast
         * when it is not present.
         */
        $this->expectException(\Error::class);

        $renderingContext = $this->createMock(RenderingContextInterface::class);

        RegisterStatusViewHelper::renderStatic([], static function () {}, $renderingContext);
    }
}
