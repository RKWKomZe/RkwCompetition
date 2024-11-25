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
class UploadControllerTest extends UnitTestCase
{
    /**
     * @var \RKW\RkwCompetition\Controller\UploadController|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder($this->buildAccessibleProxy(\RKW\RkwCompetition\Controller\UploadController::class))
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
    public function listActionFetchesAllUploadsFromRepositoryAndAssignsThemToView(): void
    {
        $allUploads = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $uploadRepository = $this->getMockBuilder(\::class)
            ->onlyMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $uploadRepository->expects(self::once())->method('findAll')->will(self::returnValue($allUploads));
        $this->subject->_set('uploadRepository', $uploadRepository);

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('uploads', $allUploads);
        $this->subject->_set('view', $view);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenUploadToView(): void
    {
        $upload = new \RKW\RkwCompetition\Domain\Model\Upload();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('upload', $upload);

        $this->subject->showAction($upload);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenUploadToUploadRepository(): void
    {
        $upload = new \RKW\RkwCompetition\Domain\Model\Upload();

        $uploadRepository = $this->getMockBuilder(\::class)
            ->onlyMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $uploadRepository->expects(self::once())->method('add')->with($upload);
        $this->subject->_set('uploadRepository', $uploadRepository);

        $this->subject->createAction($upload);
    }

    /**
     * @test
     */
    public function editActionAssignsTheGivenUploadToView(): void
    {
        $upload = new \RKW\RkwCompetition\Domain\Model\Upload();

        $view = $this->getMockBuilder(ViewInterface::class)->getMock();
        $this->subject->_set('view', $view);
        $view->expects(self::once())->method('assign')->with('upload', $upload);

        $this->subject->editAction($upload);
    }

    /**
     * @test
     */
    public function updateActionUpdatesTheGivenUploadInUploadRepository(): void
    {
        $upload = new \RKW\RkwCompetition\Domain\Model\Upload();

        $uploadRepository = $this->getMockBuilder(\::class)
            ->onlyMethods(['update'])
            ->disableOriginalConstructor()
            ->getMock();

        $uploadRepository->expects(self::once())->method('update')->with($upload);
        $this->subject->_set('uploadRepository', $uploadRepository);

        $this->subject->updateAction($upload);
    }

    /**
     * @test
     */
    public function deleteActionRemovesTheGivenUploadFromUploadRepository(): void
    {
        $upload = new \RKW\RkwCompetition\Domain\Model\Upload();

        $uploadRepository = $this->getMockBuilder(\::class)
            ->onlyMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $uploadRepository->expects(self::once())->method('remove')->with($upload);
        $this->subject->_set('uploadRepository', $uploadRepository);

        $this->subject->deleteAction($upload);
    }
}
