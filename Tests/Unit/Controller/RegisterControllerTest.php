<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Tests\Unit\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 */
class RegisterControllerTest extends UnitTestCase
{
    /**
     * @var \RKW\RkwCompetition\Controller\RegisterController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\RKW\RkwCompetition\Controller\RegisterController::class))
            ->onlyMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function listActionFetchesAllRegistersFromRepositoryAndAssignsThemToView(): void
    {
        $allRegisters = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $registerRepository = $this->getMockBuilder(\RKW\RkwCompetition\Domain\Repository\RegisterRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $registerRepository->expects(self::once())->method('findAll')->will(self::returnValue($allRegisters));
        $this->subject->_set('registerRepository', $registerRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('registers', $allRegisters);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenRegisterToView(): void
    {
        $register = new \RKW\RkwCompetition\Domain\Model\Register();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('register', $register);

        $this->subject->showAction($register);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenRegisterToRegisterRepository(): void
    {
        $register = new \RKW\RkwCompetition\Domain\Model\Register();

        $registerRepository = $this->getMockBuilder(\RKW\RkwCompetition\Domain\Repository\RegisterRepository::class)
            ->onlyMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $registerRepository->expects(self::once())->method('add')->with($register);
        $this->subject->_set('registerRepository', $registerRepository);

        $this->subject->createAction($register);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenRegisterToView(): void
    {
        $register = new \RKW\RkwCompetition\Domain\Model\Register();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('register', $register);

        $this->subject->editAction($register);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenRegisterInRegisterRepository(): void
    {
        $register = new \RKW\RkwCompetition\Domain\Model\Register();

        $registerRepository = $this->getMockBuilder(\RKW\RkwCompetition\Domain\Repository\RegisterRepository::class)
            ->onlyMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $registerRepository->expects(self::once())->method('update')->with($register);
        $this->subject->_set('registerRepository', $registerRepository);

        $this->subject->updateAction($register);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenRegisterFromRegisterRepository(): void
    {
        $register = new \RKW\RkwCompetition\Domain\Model\Register();

        $registerRepository = $this->getMockBuilder(\RKW\RkwCompetition\Domain\Repository\RegisterRepository::class)
            ->onlyMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $registerRepository->expects(self::once())->method('remove')->with($register);
        $this->subject->_set('registerRepository', $registerRepository);

        $this->subject->deleteAction($register);
    }
}
