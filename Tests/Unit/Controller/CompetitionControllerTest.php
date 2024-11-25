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
class CompetitionControllerTest extends UnitTestCase
{
    /**
     * @var \RKW\RkwCompetition\Controller\CompetitionController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\RKW\RkwCompetition\Controller\CompetitionController::class))
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
    public function listActionFetchesAllCompetitionsFromRepositoryAndAssignsThemToView(): void
    {
        $allCompetitions = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $competitionRepository = $this->getMockBuilder(\RKW\RkwCompetition\Domain\Repository\CompetitionRepository::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $competitionRepository->expects(self::once())->method('findAll')->will(self::returnValue($allCompetitions));
        $this->subject->_set('competitionRepository', $competitionRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('competitions', $allCompetitions);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenCompetitionToView(): void
    {
        $competition = new \RKW\RkwCompetition\Domain\Model\Competition();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('competition', $competition);

        $this->subject->showAction($competition);
    }
}
