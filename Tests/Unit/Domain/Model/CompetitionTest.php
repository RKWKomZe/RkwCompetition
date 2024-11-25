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
class CompetitionTest extends UnitTestCase
{
    /**
     * @var \RKW\RkwCompetition\Domain\Model\Competition|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \RKW\RkwCompetition\Domain\Model\Competition::class,
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
    public function getTitleReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle(): void
    {
        $this->subject->setTitle('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('title'));
    }

    /**
     * @test
     */
    public function getRegisterStartReturnsInitialValueForDateTime(): void
    {
        self::assertEquals(
            null,
            $this->subject->getRegisterStart()
        );
    }

    /**
     * @test
     */
    public function setRegisterStartForDateTimeSetsRegisterStart(): void
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setRegisterStart($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('registerStart'));
    }

    /**
     * @test
     */
    public function getRegisterEndReturnsInitialValueForDateTime(): void
    {
        self::assertEquals(
            null,
            $this->subject->getRegisterEnd()
        );
    }

    /**
     * @test
     */
    public function setRegisterEndForDateTimeSetsRegisterEnd(): void
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setRegisterEnd($dateTimeFixture);

        self::assertEquals($dateTimeFixture, $this->subject->_get('registerEnd'));
    }

    /**
     * @test
     */
    public function getJuryEndReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getJuryEnd()
        );
    }

    /**
     * @test
     */
    public function setJuryEndForStringSetsJuryEnd(): void
    {
        $this->subject->setJuryEnd('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('juryEnd'));
    }

    /**
     * @test
     */
    public function getFileRemovalEndReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getFileRemovalEnd()
        );
    }

    /**
     * @test
     */
    public function setFileRemovalEndForStringSetsFileRemovalEnd(): void
    {
        $this->subject->setFileRemovalEnd('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('fileRemovalEnd'));
    }

    /**
     * @test
     */
    public function getJuryAddDataReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getJuryAddData()
        );
    }

    /**
     * @test
     */
    public function setJuryAddDataForStringSetsJuryAddData(): void
    {
        $this->subject->setJuryAddData('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('juryAddData'));
    }

    /**
     * @test
     */
    public function getLinkJuryDeclarationConfidentReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getLinkJuryDeclarationConfident()
        );
    }

    /**
     * @test
     */
    public function setLinkJuryDeclarationConfidentForStringSetsJuryDeclarationConfident(): void
    {
        $this->subject->setLinkJuryDeclarationConfident('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('linkJuryDeclarationConfident'));
    }

    /**
     * @test
     */
    public function getAllowTeamParticipationReturnsInitialValueForBool(): void
    {
        self::assertFalse($this->subject->getAllowTeamParticipation());
    }

    /**
     * @test
     */
    public function setAllowTeamParticipationForBoolSetsAllowTeamParticipation(): void
    {
        $this->subject->setAllowTeamParticipation(true);

        self::assertEquals(true, $this->subject->_get('allowTeamParticipation'));
    }

    /**
     * @test
     */
    public function getReminderIntervalReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getReminderInterval()
        );
    }

    /**
     * @test
     */
    public function setReminderIntervalForIntSetsReminderInterval(): void
    {
        $this->subject->setReminderInterval(12);

        self::assertEquals(12, $this->subject->_get('reminderInterval'));
    }

    /**
     * @test
     */
    public function getLinkCondParticipationReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getLinkCondParticipation()
        );
    }

    /**
     * @test
     */
    public function setLinkCondParticipationForStringSetsLinkCondParticipation(): void
    {
        $this->subject->setLinkCondParticipation('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('linkCondParticipation'));
    }

    /**
     * @test
     */
    public function getLinkPrivacyReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getLinkPrivacy()
        );
    }

    /**
     * @test
     */
    public function setLinkPrivacyForStringSetsLinkPrivacy(): void
    {
        $this->subject->setLinkPrivacy('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('linkPrivacy'));
    }

    /**
     * @test
     */
    public function getAdminMemberReturnsInitialValueForBackendUser(): void
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getAdminMember()
        );
    }

    /**
     * @test
     */
    public function setAdminMemberForObjectStorageContainingBackendUserSetsAdminMember(): void
    {
        $adminMember = new \Madj2k\FeRegister\Domain\Model\BackendUser();
        $objectStorageHoldingExactlyOneAdminMember = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneAdminMember->attach($adminMember);
        $this->subject->setAdminMember($objectStorageHoldingExactlyOneAdminMember);

        self::assertEquals($objectStorageHoldingExactlyOneAdminMember, $this->subject->_get('adminMember'));
    }

    /**
     * @test
     */
    public function addAdminMemberToObjectStorageHoldingAdminMember(): void
    {
        $adminMember = new \Madj2k\FeRegister\Domain\Model\BackendUser();
        $adminMemberObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $adminMemberObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($adminMember));
        $this->subject->_set('adminMember', $adminMemberObjectStorageMock);

        $this->subject->addAdminMember($adminMember);
    }

    /**
     * @test
     */
    public function removeAdminMemberFromObjectStorageHoldingAdminMember(): void
    {
        $adminMember = new \Madj2k\FeRegister\Domain\Model\BackendUser();
        $adminMemberObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $adminMemberObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($adminMember));
        $this->subject->_set('adminMember', $adminMemberObjectStorageMock);

        $this->subject->removeAdminMember($adminMember);
    }

    /**
     * @test
     */
    public function getJuryMemberReturnsInitialValueForFrontendUser(): void
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getJuryMember()
        );
    }

    /**
     * @test
     */
    public function setJuryMemberForObjectStorageContainingFrontendUserSetsJuryMember(): void
    {
        $juryMember = new \Madj2k\FeRegister\Domain\Model\FrontendUser();
        $objectStorageHoldingExactlyOneJuryMember = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneJuryMember->attach($juryMember);
        $this->subject->setJuryMember($objectStorageHoldingExactlyOneJuryMember);

        self::assertEquals($objectStorageHoldingExactlyOneJuryMember, $this->subject->_get('juryMember'));
    }

    /**
     * @test
     */
    public function addJuryMemberToObjectStorageHoldingJuryMember(): void
    {
        $juryMember = new \Madj2k\FeRegister\Domain\Model\FrontendUser();
        $juryMemberObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $juryMemberObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($juryMember));
        $this->subject->_set('juryMember', $juryMemberObjectStorageMock);

        $this->subject->addJuryMember($juryMember);
    }

    /**
     * @test
     */
    public function removeJuryMemberFromObjectStorageHoldingJuryMember(): void
    {
        $juryMember = new \Madj2k\FeRegister\Domain\Model\FrontendUser();
        $juryMemberObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $juryMemberObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($juryMember));
        $this->subject->_set('juryMember', $juryMemberObjectStorageMock);

        $this->subject->removeJuryMember($juryMember);
    }

    /**
     * @test
     */
    public function getGroupForJuryReturnsInitialValueForFrontendUserGroup(): void
    {
    }

    /**
     * @test
     */
    public function setGroupForJuryForFrontendUserGroupSetsGroupForJury(): void
    {
    }

    /**
     * @test
     */
    public function getGroupForUserReturnsInitialValueForFrontendUserGroup(): void
    {
    }

    /**
     * @test
     */
    public function setGroupForUserForFrontendUserGroupSetsGroupForUser(): void
    {
    }

    /**
     * @test
     */
    public function getSectorsReturnsInitialValueForSector(): void
    {
        self::assertEquals(
            null,
            $this->subject->getSectors()
        );
    }

    /**
     * @test
     */
    public function setSectorsForSectorSetsSectors(): void
    {
        $sectorsFixture = new \RKW\RkwCompetition\Domain\Model\Sector();
        $this->subject->setSectors($sectorsFixture);

        self::assertEquals($sectorsFixture, $this->subject->_get('sectors'));
    }

    /**
     * @test
     */
    public function getRegisterReturnsInitialValueForRegister(): void
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        self::assertEquals(
            $newObjectStorage,
            $this->subject->getRegister()
        );
    }

    /**
     * @test
     */
    public function setRegisterForObjectStorageContainingRegisterSetsRegister(): void
    {
        $register = new \RKW\RkwCompetition\Domain\Model\Register();
        $objectStorageHoldingExactlyOneRegister = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneRegister->attach($register);
        $this->subject->setRegister($objectStorageHoldingExactlyOneRegister);

        self::assertEquals($objectStorageHoldingExactlyOneRegister, $this->subject->_get('register'));
    }

    /**
     * @test
     */
    public function addRegisterToObjectStorageHoldingRegister(): void
    {
        $register = new \RKW\RkwCompetition\Domain\Model\Register();
        $registerObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['attach'])
            ->disableOriginalConstructor()
            ->getMock();

        $registerObjectStorageMock->expects(self::once())->method('attach')->with(self::equalTo($register));
        $this->subject->_set('register', $registerObjectStorageMock);

        $this->subject->addRegister($register);
    }

    /**
     * @test
     */
    public function removeRegisterFromObjectStorageHoldingRegister(): void
    {
        $register = new \RKW\RkwCompetition\Domain\Model\Register();
        $registerObjectStorageMock = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->onlyMethods(['detach'])
            ->disableOriginalConstructor()
            ->getMock();

        $registerObjectStorageMock->expects(self::once())->method('detach')->with(self::equalTo($register));
        $this->subject->_set('register', $registerObjectStorageMock);

        $this->subject->removeRegister($register);
    }
}
