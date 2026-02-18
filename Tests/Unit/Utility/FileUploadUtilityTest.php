<?php

namespace RKW\RkwCompetition\Tests\Unit\Utility;

use PHPUnit\Framework\TestCase;
use RKW\RkwCompetition\Utility\FileUploadUtility;

class FileUploadUtilityTest extends TestCase
{
    /**
     * @return void
     */
    public function testCheckFileFormUploadReturnsFalseWhenFileArrayIsEmpty(): void
    {
        /**
         * Scenario:
         *
         * Given an empty file upload array
         * When checkFileFormUpload is called
         * Then the method returns false
         */

        $fileArray = [];
        $result = FileUploadUtility::checkFileFormUpload($fileArray);
        $this->assertFalse($result);
    }

    /**
     * Tests that the method returns false when the 'error' key has value 4 (no file uploaded).
     */
    public function testCheckFileFormUploadReturnsFalseWhenErrorIsFour(): void
    {
        /**
         * Scenario:
         *
         * Given a file upload array
         * Given the error code is 4 (no file uploaded)
         * When checkFileFormUpload is called
         * Then the method returns false
         */

        $fileArray = ['error' => 4];
        $result = FileUploadUtility::checkFileFormUpload($fileArray);
        $this->assertFalse($result);
    }

    /**
     * Tests that the method returns true when the file array is valid and 'error' is not 4.
     */
    public function testCheckFileFormUploadReturnsTrueForValidFileUpload(): void
    {
        /**
         * Scenario:
         *
         * Given a valid file upload array
         * Given the error code is 0
         * When checkFileFormUpload is called
         * Then the method returns true
         */

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
     * @return void
     */
    public function testGetShortenedMimeTypeReturnsCorrectType(): void
    {
        /**
         * Scenario:
         *
         * Given a file array with MIME type "application/json"
         * When getShortenedMimeType is called
         * Then the method returns "json"
         */

        $fileArray = [
            'type' => 'application/json',
        ];
        $result = FileUploadUtility::getShortenedMimeType($fileArray);
        $this->assertEquals('json', $result);
    }

    /**
     * @return void
     */
    public function testGetShortenedMimeTypeReturnsCorrectTypeForText(): void
    {
        /**
         * Scenario:
         *
         * Given a file array with MIME type "text/plain"
         * When getShortenedMimeType is called
         * Then the method returns "plain"
         */

        $fileArray = [
            'type' => 'text/plain',
        ];
        $result = FileUploadUtility::getShortenedMimeType($fileArray);
        $this->assertEquals('plain', $result);
    }

    /**
     * @return void
     */
    public function testGetShortenedMimeTypeHandlesInvalidFormat(): void
    {
        /**
         * Scenario:
         *
         * Given a file array with an invalid MIME type format without a "/" separator
         * When getShortenedMimeType is called
         * Then the method returns an empty string
         */

        $fileArray = [
            'type' => 'invalidFormat',
        ];
        $result = FileUploadUtility::getShortenedMimeType($fileArray);
        $this->assertEquals('', $result);
    }
}
