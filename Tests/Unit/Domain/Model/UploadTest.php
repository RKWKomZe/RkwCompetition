<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Tests\Unit\Domain\Model;

use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 */
class UploadTest extends UnitTestCase
{
    /**
     * @var \RKW\RkwCompetition\Domain\Model\Upload|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \RKW\RkwCompetition\Domain\Model\Upload::class,
            ['dummy']
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getAbstractReturnsInitialValueForFileReference(): void
    {
        self::assertEquals(
            null,
            $this->subject->getAbstract()
        );
    }

    /**
     * @test
     */
    public function setAbstractForFileReferenceSetsAbstract(): void
    {
        $fileReferenceFixture = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $this->subject->setAbstract($fileReferenceFixture);

        self::assertEquals($fileReferenceFixture, $this->subject->_get('abstract'));
    }

    /**
     * @test
     */
    public function getFullReturnsInitialValueForFileReference(): void
    {
        self::assertEquals(
            null,
            $this->subject->getFull()
        );
    }

    /**
     * @test
     */
    public function setFullForFileReferenceSetsFull(): void
    {
        $fileReferenceFixture = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $this->subject->setFull($fileReferenceFixture);

        self::assertEquals($fileReferenceFixture, $this->subject->_get('full'));
    }

    /**
     * @test
     */
    public function getRemarkReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getRemark()
        );
    }

    /**
     * @test
     */
    public function setRemarkForStringSetsRemark(): void
    {
        $this->subject->setRemark('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('remark'));
    }
}
