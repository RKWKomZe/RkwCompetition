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
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference|null
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $abstract = null;

    /**
     * full
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference|null
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
    protected array $fileAbstractUploadArray = [];

    /**
     * ### only for form upload ###
     *
     * @var array
     */
    protected array $fileFullUploadArray = [];

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
     * Deletes the abstract file
     */
    public function unsetAbstract() {
        $this->abstract = null;
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
     * @param mixed $full
     * @return void
     */
    public function setFull($full)
    {
        $this->full = $full;
    }

    /**
     * Deletes the full file
     */
    public function unsetFull() {
        $this->full = null;
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
     * @return array $fileAbstractUploadArray
     */
    public function getFileAbstractUploadArray(): array
    {
        return $this->fileAbstractUploadArray;
    }


    /**
     * Sets the fileAbstractUploadArray
     * ### only for form upload ###
     *
     * @param array $fileAbstractUploadArray
     * @return void
     */
    public function setFileAbstractUploadArray(array $fileAbstractUploadArray): void
    {
        $this->fileAbstractUploadArray = $fileAbstractUploadArray;
    }

    /**
     * Returns the fileFull
     * ### only for form upload ###
     *
     * @return array $fileFullUploadArray
     */
    public function getFileFullUploadArray(): array
    {
        return $this->fileFullUploadArray;
    }


    /**
     * Sets the fileFull
     * ### only for form upload ###
     *
     * @param array $fileFullUploadArray
     * @return void
     */
    public function setFileFullUploadArray(array $fileFullUploadArray): void
    {
        $this->fileFullUploadArray = $fileFullUploadArray;
    }
}
