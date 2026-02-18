<?php
declare(strict_types=1);

namespace RKW\RkwCompetition\Tests\Integration\Persistence;

use RKW\RkwCompetition\Persistence\FileHandler;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * @group integration
 */
final class FileHandlerTest extends FunctionalTestCase
{
    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/FileHandlerTest/Fixtures';


    private string $uniq;

    private ResourceFactory $resourceFactory;
    private ResourceStorage $storage;

    private FileHandler $fileHandler;

    /**
     * @return void
     * @throws \Nimut\TestingFramework\Exception\Exception
     * @throws \Random\RandomException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->importDataSet(self::FIXTURE_PATH . '/Database/Global.xml');

        /*
        $this->setUpFrontendRootPage(
            1,
            [
                'EXT:rkw_competition/Configuration/TypoScript/setup.txt',
                'EXT:rkw_competition/Configuration/TypoScript/constants.txt',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
            ]
        );
        */

        // Critical: prevents "isAdmin() on null" in permission checks
        $this->setUpBackendUserFromFixture(999);

        // some unique test group name
        $this->uniq = 'PhpUnit_' . gmdate('Ymd_His') . '_' . substr(bin2hex(random_bytes(3)), 0, 6);

        $this->resourceFactory = ResourceFactory::getInstance();
        $this->storage = $this->resourceFactory->getStorageObject(1);

        $this->fileHandler = new FileHandler();

        // Ensure deterministic folder base for these tests
        $this->fileHandler->setDefaultUploadFolder('user_upload/tx_rkwcompetition');
        // Keep storage uid default '1' (avoid setResourceStorageUid() unless needed)

    }

    protected function tearDown(): void
    {
        // best-effort cleanup of uniq subtree
        try {
            $base = $this->getOrCreateFolderChain(['user_upload', 'tx_rkwcompetition']);
            if ($base->hasFolder($this->uniq)) {
                $base->getSubfolder($this->uniq)->delete(true);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        parent::tearDown();
    }

    /**
     * @test
     */
    public function createFolderIdentifierByFolderNameBuildsCombinedIdentifier(): void
    {
        /**
         * Scenario:
         *
         * Given a FileHandler with default resourceStorageUid "1"
         * And the default upload folder "user_upload/tx_rkwcompetition"
         * When createFolderIdentifierByFolderName() is called with the folder name "abc"
         * Then a combined FAL identifier string is returned
         * And the identifier follows the pattern "<storageUid>:<defaultUploadFolder>/<folderName>"
         * And the resulting identifier equals "1:user_upload/tx_rkwcompetition/abc"
         */

        $identifier = $this->fileHandler->createFolderIdentifierByFolderName('abc');

        $this->assertSame('1:user_upload/tx_rkwcompetition/abc', $identifier);
    }


    /**
     * @test
     */
    public function removeAllFilesOfFolderFromHddReturnsTrueWhenFolderDoesNotExist(): void
    {
        /**
         * Scenario:
         *
         * Given a folder identifier that does not exist in the configured storage
         * When removeAllFilesOfFolderFromHdd() is called with this identifier
         * Then no exception is thrown
         * And the method handles the missing folder gracefully
         * And the method returns true to indicate successful handling
         */
        $folderIdentifier = $this->fileHandler->createFolderIdentifierByFolderName('does-not-exist-' . $this->uniq);

        $this->assertTrue($this->fileHandler->removeAllFilesOfFolderFromHdd($folderIdentifier));
    }

    /**
     * @test
     */
    public function removeFolderFromHddByIdentifierReturnsTrueWhenFolderDoesNotExist(): void
    {
        /**
         * Scenario:
         *
         * Given a folder identifier that does not exist in the configured storage
         * When removeFolderFromHddByIdentifier() is called with this identifier
         * Then the method catches the underlying exception for the missing folder
         * And no exception is propagated to the caller
         * And the method returns true to signal that the removal is effectively handled
         */
        $folderIdentifier = $this->fileHandler->createFolderIdentifierByFolderName('does-not-exist-' . $this->uniq);

        $this->assertTrue($this->fileHandler->removeFolderFromHddByIdentifier($folderIdentifier));
    }


    /**
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function removeAllFilesOfFolderFromHddDeletesAllFiles(): void
    {
        /**
         * Scenario:
         *
         * Given a folder identifier that resolves to an existing folder
         * And the folder contains two file objects
         * And removeFileFromHdd() is mocked to avoid real filesystem interaction
         * When removeAllFilesOfFolderFromHdd() is called with this identifier
         * Then getFiles() is called exactly once on the folder
         * And removeFileFromHdd() is called exactly twice
         * And each file returned by getFiles() is passed to removeFileFromHdd() in order
         * And the method returns true after processing all files
         */
        $file1 = $this->createMock(File::class);
        $file2 = $this->createMock(File::class);

        $folder = $this->createMock(Folder::class);
        $folder->expects($this->once())
            ->method('getFiles')
            ->willReturn([$file1, $file2]);

        $resourceFactory = $this->createMock(ResourceFactory::class);
        $resourceFactory->expects($this->once())
            ->method('getFolderObjectFromCombinedIdentifier')
            ->with('1:user_upload/tx_rkwcompetition/abc')
            ->willReturn($folder);

        // Partial mock: wir wollen removeFileFromHdd zählen, aber nicht wirklich FAL anfassen
        $sut = $this->getMockBuilder(FileHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['removeFileFromHdd'])
            ->getMock();

        // ResourceFactory in die Instanz injizieren (Property ist protected -> via Reflection)
        $ref = new \ReflectionClass($sut);
        $prop = $ref->getProperty('resourceFactory');
        $prop->setAccessible(true);
        $prop->setValue($sut, $resourceFactory);

        $expected = [$file1, $file2];
        $callIndex = 0;
        $sut->expects($this->exactly(2))
            ->method('removeFileFromHdd')
            ->willReturnCallback(function ($arg) use (&$callIndex, $expected) {
                $this->assertSame($expected[$callIndex], $arg);
                $callIndex++;
                return true;
            });

        $this->assertTrue($sut->removeAllFilesOfFolderFromHdd('1:user_upload/tx_rkwcompetition/abc'));
    }


    /**
     * @test
     * @return void
     * @throws \ReflectionException
     */
    public function removeAllFilesOfFolderFromHddReturnsTrueWhenFolderCannotBeResolved(): void
    {
        /**
         * Scenario:
         *
         * Given a folder identifier that cannot be resolved by the ResourceFactory
         * And getFolderObjectFromCombinedIdentifier() throws an exception
         * When removeAllFilesOfFolderFromHdd() is called with this identifier
         * Then the exception is caught internally
         * And removeFileFromHdd() is never called
         * And the method returns true to signal graceful handling of the missing folder
         */
        $resourceFactory = $this->createMock(ResourceFactory::class);
        $resourceFactory->expects($this->once())
            ->method('getFolderObjectFromCombinedIdentifier')
            ->willThrowException(new \RuntimeException('not found'));

        $sut = $this->getMockBuilder(FileHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['removeFileFromHdd'])
            ->getMock();

        $ref = new \ReflectionClass($sut);
        $prop = $ref->getProperty('resourceFactory');
        $prop->setAccessible(true);
        $prop->setValue($sut, $resourceFactory);

        // darf nie aufgerufen werden
        $sut->expects($this->never())->method('removeFileFromHdd');

        $this->assertTrue($sut->removeAllFilesOfFolderFromHdd('1:user_upload/tx_rkwcompetition/missing'));
    }


    /**
     * Creates (if missing) fileadmin/user_upload/tx_rkwcompetition/<uniq>/ and returns the <uniq> Folder.
     */
    private function ensureUploadSubtreeExistsAndReturnUniqFolder(string $uniq): Folder
    {
        $base = $this->getOrCreateFolderChain(['user_upload', 'tx_rkwcompetition']);

        if ($base->hasFolder($uniq)) {
            return $base->getSubfolder($uniq);
        }

        return $base->createFolder($uniq);
    }

    /**
     * Creates (if missing) a folder chain under the storage root and returns the leaf folder.
     * Example: ['user_upload', 'tx_rkwcompetition'].
     */
    private function getOrCreateFolderChain(array $segments): Folder
    {
        $folder = $this->storage->getRootLevelFolder();

        foreach ($segments as $segment) {
            if ($folder->hasFolder($segment)) {
                $folder = $folder->getSubfolder($segment);
                continue;
            }
            $folder = $folder->createFolder($segment);
        }

        return $folder;
    }

    /**
     * Adds a temporary local file into a FAL folder and returns the created FAL File.
     */
    private function addTempFileToFolder(Folder $folder, string $targetName, string $contents): File
    {
        $tmp = tempnam(sys_get_temp_dir(), 't3fh_');
        if ($tmp === false) {
            $this->fail('Could not create temp file');
        }

        file_put_contents($tmp, $contents);

        // conflict mode: rename (matches your FileHandler default)
        return $folder->addFile($tmp, $targetName, 'rename');
    }



}
