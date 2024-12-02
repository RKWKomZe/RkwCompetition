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
class RegisterTest extends UnitTestCase
{
    /**
     * @var \RKW\RkwCompetition\Domain\Model\Register|MockObject|AccessibleObjectInterface
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = $this->getAccessibleMock(
            \RKW\RkwCompetition\Domain\Model\Register::class,
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
    public function getSalutationReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getSalutation()
        );
    }

    /**
     * @test
     */
    public function setSalutationForStringSetsSalutation(): void
    {
        $this->subject->setSalutation('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('salutation'));
    }

    /**
     * @test
     */
    public function getTitleReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForIntSetsTitle(): void
    {
        $this->subject->setTitle(12);

        self::assertEquals(12, $this->subject->_get('title'));
    }

    /**
     * @test
     */
    public function getFirstNameReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getFirstName()
        );
    }

    /**
     * @test
     */
    public function setFirstNameForStringSetsFirstName(): void
    {
        $this->subject->setFirstName('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('firstName'));
    }

    /**
     * @test
     */
    public function getLastNameReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getLastName()
        );
    }

    /**
     * @test
     */
    public function setLastNameForStringSetsLastName(): void
    {
        $this->subject->setLastName('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('lastName'));
    }

    /**
     * @test
     */
    public function getInstitutionReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getInstitution()
        );
    }

    /**
     * @test
     */
    public function setInstitutionForStringSetsInstitution(): void
    {
        $this->subject->setInstitution('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('institution'));
    }

    /**
     * @test
     */
    public function getAddressReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getAddress()
        );
    }

    /**
     * @test
     */
    public function setAddressForStringSetsAddress(): void
    {
        $this->subject->setAddress('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('address'));
    }

    /**
     * @test
     */
    public function getZipReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getZip()
        );
    }

    /**
     * @test
     */
    public function setZipForIntSetsZip(): void
    {
        $this->subject->setZip(12);

        self::assertEquals(12, $this->subject->_get('zip'));
    }

    /**
     * @test
     */
    public function getCityReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getCity()
        );
    }

    /**
     * @test
     */
    public function setCityForStringSetsCity(): void
    {
        $this->subject->setCity('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('city'));
    }

    /**
     * @test
     */
    public function getContributionTitleReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getContributionTitle()
        );
    }

    /**
     * @test
     */
    public function setContributionTitleForStringSetsContributionTitle(): void
    {
        $this->subject->setContributionTitle('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('contributionTitle'));
    }

    /**
     * @test
     */
    public function getTypeOfWorkReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getTypeOfWork()
        );
    }

    /**
     * @test
     */
    public function setTypeOfWorkForIntSetsTypeOfWork(): void
    {
        $this->subject->setTypeOfWork(12);

        self::assertEquals(12, $this->subject->_get('typeOfWork'));
    }

    /**
     * @test
     */
    public function getCompetitionAreaReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getCompetitionArea()
        );
    }

    /**
     * @test
     */
    public function setCompetitionAreaForIntSetsCompetitionArea(): void
    {
        $this->subject->setCompetitionArea(12);

        self::assertEquals(12, $this->subject->_get('competitionArea'));
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

    /**
     * @test
     */
    public function getPrivacyReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getPrivacy()
        );
    }

    /**
     * @test
     */
    public function setPrivacyForIntSetsPrivacy(): void
    {
        $this->subject->setPrivacy(12);

        self::assertEquals(12, $this->subject->_get('privacy'));
    }

    /**
     * @test
     */
    public function getConditionsOfParticipationReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getConditionsOfParticipation()
        );
    }

    /**
     * @test
     */
    public function setConditionsOfParticipationForStringSetsConditionsOfParticipation(): void
    {
        $this->subject->setConditionsOfParticipation('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('conditionsOfParticipation'));
    }

    /**
     * @test
     */
    public function getIsGroupWorkReturnsInitialValueForInt(): void
    {
        self::assertSame(
            0,
            $this->subject->getIsGroupWork()
        );
    }

    /**
     * @test
     */
    public function setIsGroupWorkForIntSetsIsGroupWork(): void
    {
        $this->subject->setIsGroupWork(12);

        self::assertEquals(12, $this->subject->_get('isGroupWork'));
    }

    /**
     * @test
     */
    public function getGroupWorkInsuranceReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getGroupWorkInsurance()
        );
    }

    /**
     * @test
     */
    public function setGroupWorkInsuranceForStringSetsGroupWorkInsurance(): void
    {
        $this->subject->setGroupWorkInsurance('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('groupWorkInsurance'));
    }

    /**
     * @test
     */
    public function getGroupWorkAddPersonsReturnsInitialValueForString(): void
    {
        self::assertSame(
            '',
            $this->subject->getGroupWorkAddPersons()
        );
    }

    /**
     * @test
     */
    public function setGroupWorkAddPersonsForStringSetsGroupWorkAddPersons(): void
    {
        $this->subject->setGroupWorkAddPersons('Conceived at T3CON10');

        self::assertEquals('Conceived at T3CON10', $this->subject->_get('groupWorkAddPersons'));
    }

    /**
     * @test
     */
    public function getUploadReturnsInitialValueForUpload(): void
    {
        self::assertEquals(
            null,
            $this->subject->getUpload()
        );
    }

    /**
     * @test
     */
    public function setUploadForUploadSetsUpload(): void
    {
        $uploadFixture = new \RKW\RkwCompetition\Domain\Model\Upload();
        $this->subject->setUpload($uploadFixture);

        self::assertEquals($uploadFixture, $this->subject->_get('upload'));
    }
}
