<?php

namespace RKW\RkwCompetition\Tests\Unit\Utility;

use PHPUnit\Framework\TestCase;
use RKW\RkwCompetition\Utility\FileUploadUtility;

class FileUploadUtilityTest extends TestCase
{
    /**
     * Tests that the method returns false when the input array is empty.
     */
    public function testCheckFileFormUploadReturnsFalseWhenFileArrayIsEmpty(): void
    {
        $fileArray = [];
        $result = FileUploadUtility::checkFileFormUpload($fileArray);
        $this->assertFalse($result);
    }

    /**
     * Tests that the method returns false when the 'error' key has the value 4 (no file uploaded).
     */
    public function testCheckFileFormUploadReturnsFalseWhenErrorIsFour(): void
    {
        $fileArray = ['error' => 4];
        $result = FileUploadUtility::checkFileFormUpload($fileArray);
        $this->assertFalse($result);
    }

    /**
     * Tests that the method returns true when the file array is valid and 'error' is not 4.
     */
    public function testCheckFileFormUploadReturnsTrueForValidFileUpload(): void
    {
        $fileArray = [
            'name' => 'example.txt',
            'type' => 'text/plain',
            'tmp_name' => '/tmp/phpYzdqkD',
            'error' => 0,
            'size' => 123
        ];
        $result = FileUploadUtility::checkFileFormUpload($fileArray);
        $this->assertTrue($result);
    }

    /**
     * Tests that getShortenedMimeType returns correct MIME type without "application/".
     */
    public function testGetShortenedMimeTypeReturnsCorrectType(): void
    {
        $fileArray = [
            'type' => 'application/json',
        ];
        $result = FileUploadUtility::getShortenedMimeType($fileArray);
        $this->assertEquals('json', $result);
    }

    /**
     * Tests that getShortenedMimeType returns correct MIME type when "text/" is provided.
     */
    public function testGetShortenedMimeTypeReturnsCorrectTypeForText(): void
    {
        $fileArray = [
            'type' => 'text/plain',
        ];
        $result = FileUploadUtility::getShortenedMimeType($fileArray);
        $this->assertEquals('plain', $result);
    }

    /**
     * Tests that getShortenedMimeType handles incorrect MIME type format gracefully.
     */
    public function testGetShortenedMimeTypeHandlesInvalidFormat(): void
    {
        $fileArray = [
            'type' => 'invalidFormat',
        ];
        $result = FileUploadUtility::getShortenedMimeType($fileArray);
        $this->assertEquals('', $result);
    }
}
