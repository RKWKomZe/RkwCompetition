<?php

declare(strict_types=1);

namespace RKW\RkwCompetition\Domain\Model;


/**
 * This file is part of the "RKW Competition" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2024 Maximilian Fäßler <maximilian@faesslerweb.de>, RKW Kompetenzzentrum
 */

/**
 * Upload
 */
class Upload extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * abstract
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $abstract = null;

    /**
     * full
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $full = null;

    /**
     * remark
     *
     * @var string
     */
    protected $remark = '';

    /**
     * ### only for form upload ###
     *
     * @var array
     */
    protected array $fileAbstract = [];

    /**
     * ### only for form upload ###
     *
     * @var array
     */
    protected array $fileFull = [];

    /**
     * Returns the abstract
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * Sets the abstract
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $abstract
     * @return void
     */
    public function setAbstract(\TYPO3\CMS\Extbase\Domain\Model\FileReference $abstract)
    {
        $this->abstract = $abstract;
    }

    /**
     * Returns the full
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getFull()
    {
        return $this->full;
    }

    /**
     * Sets the full
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $full
     * @return void
     */
    public function setFull(\TYPO3\CMS\Extbase\Domain\Model\FileReference $full)
    {
        $this->full = $full;
    }

    /**
     * Returns the remark
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Sets the remark
     *
     * @param string $remark
     * @return void
     */
    public function setRemark(string $remark)
    {
        $this->remark = $remark;
    }

    /**
     * Returns the fileAbstract
     * ### only for form upload ###
     *
     * @return array $fileAbstract
     */
    public function getFileAbstract(): array
    {
        return $this->fileAbstract;
    }


    /**
     * Sets the fileAbstract
     * ### only for form upload ###
     *
     * @param array $fileAbstract
     * @return void
     */
    public function setFileAbstract(array $fileAbstract): void
    {
        $this->fileAbstract = $fileAbstract;
    }

    /**
     * Returns the fileFull
     * ### only for form upload ###
     *
     * @return array $fileFull
     */
    public function getFileFull(): array
    {
        return $this->fileFull;
    }


    /**
     * Sets the fileFull
     * ### only for form upload ###
     *
     * @param array $fileFull
     * @return void
     */
    public function setFileFull(array $fileFull): void
    {
        $this->fileFull = $fileFull;
    }
}
