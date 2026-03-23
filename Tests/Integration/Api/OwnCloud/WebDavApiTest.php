<?php
declare(strict_types=1);

namespace RKW\RkwCompetition\Tests\Integration\Api\OwnCloud;

use GuzzleHttp\Psr7\Response;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use RKW\RkwCompetition\Api\OwnCloud\GroupsApi;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use RKW\RkwCompetition\Api\OwnCloud\ShareApi;
use RKW\RkwCompetition\Api\OwnCloud\UsersApi;
use RKW\RkwCompetition\Api\OwnCloud\WebDavApi;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @group integration
 */
final class WebDavApiTest extends FunctionalTestCase
{
    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/WebDavApiTest/Fixtures';

    private WebDavApi $api;
    private string $uniq;

    /**
     * @return void
     * @throws \Nimut\TestingFramework\Exception\Exception
     * @throws \Random\RandomException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->importDataSet(self::FIXTURE_PATH . '/Database/Global.xml');

        $this->setUpFrontendRootPage(
            1,
            [
                'EXT:rkw_competition/Configuration/TypoScript/setup.txt',
                'EXT:rkw_competition/Configuration/TypoScript/constants.txt',
                self::FIXTURE_PATH . '/Frontend/Configuration/Rootpage.typoscript',
            ]
        );

        // some unique test group name
        $this->uniq = 'PhpUnit_' . gmdate('Ymd_His') . '_' . substr(bin2hex(random_bytes(3)), 0, 6);


        $this->api = new WebDavApi();
        $this->api->queryType = 'ocs';

        $this->skipIfOwnCloudUnavailable();

    }


    /**
     * @group owncloud
     */
    public function testAddFolderCreatesFolder(): void
    {
        /**
         * Scenario:
         *
         * Given a running OwnCloud instance
         * And a unique folder path inside "/PhpUnitFixtures"
         *
         * When the parent folder structure is ensured via addFolderRecursive()
         * And a new leaf folder is created via MKCOL using addFolder()
         * Then the MKCOL request returns either 201 (Created)
         * Or 405 (Method Not Allowed) if the folder already exists
         *
         * When the created folder is requested via PROPFIND
         * Then the response status code is 207 (Multi-Status)
         * Indicating that the folder exists in WebDAV
         *
         * Finally the created test folder structure is removed
         * So that no test artifacts remain in OwnCloud
         */

        $folder = ['PhpUnitFixtures', $this->uniq, 'mkcol'];

        try {
            // Ensure base exists (ignore if already exists)
            $this->api->addFolderRecursive(['PhpUnitFixtures', $this->uniq]);

            // Create leaf folder
            $res = $this->api->addFolder($folder);
            $code = (int)array_key_first($res);

            // 201 Created is expected; 405 Method Not Allowed can happen if folder already exists
            $this->assertTrue(in_array($code, [201, 405], true), 'MKCOL should return 201 or 405, got ' . $code);

            // Verify folder exists via PROPFIND (207)
            $this->api->apiMethod = 'PROPFIND';
            $probe = $this->api->doApiRequest(WebDavApi::API_PATH . implode('/', $folder));
            $probeCode = (int)array_key_first($probe);

            $this->assertSame(207, $probeCode, 'PROPFIND should return 207 for existing folder');

        } finally {
            // Cleanup: delete the whole uniq subtree
            try { $this->api->removeFileOrFolder(['PhpUnitFixtures', $this->uniq]); } catch (\Throwable $e) {}
        }
    }


    /**
     * @group owncloud
     */
    public function testAddFolderRecursiveCreatesNestedFolders(): void
    {
        /**
         * Scenario:
         *
         * Given a running OwnCloud instance
         * And a nested folder path inside "/PhpUnitFixtures"
         * Consisting of multiple levels that may not yet exist
         *
         * When addFolderRecursive() is called with that nested path
         * Then missing intermediate folders are created step by step
         * And the final PROPFIND result indicates that the leaf folder exists
         * (status code 207 Multi-Status)
         *
         * When the leaf folder is explicitly requested via PROPFIND
         * Then the response status code is 207
         * Confirming that the complete nested folder structure has been created
         *
         * Finally the created test folder structure is removed
         * So that no test artifacts remain in OwnCloud
         */

        $nested = ['PhpUnitFixtures', $this->uniq, 'a', 'b', 'c'];

        try {
            $res = $this->api->addFolderRecursive($nested);
            $code = (int)array_key_first($res);

            // After recursion, last PROPFIND should be 207 (exists)
            $this->assertSame(207, $code, 'Expected last PROPFIND to be 207, got ' . $code);

            // Extra: verify leaf exists
            $this->api->apiMethod = 'PROPFIND';
            $probe = $this->api->doApiRequest(WebDavApi::API_PATH . implode('/', $nested));
            $this->assertSame(207, (int)array_key_first($probe));

        } finally {
            try { $this->api->removeFileOrFolder(['PhpUnitFixtures', $this->uniq]); } catch (\Throwable $e) {}
        }
    }


    /**
     * @group owncloud
     */
    public function testRemoveFileOrFolderDeletesFolder(): void
    {
        /**
         * Scenario:
         *
         * Given a running OwnCloud instance
         * And a unique folder path inside "/PhpUnitFixtures"
         * And the folder has been created via addFolderRecursive()
         *
         * When the folder is deleted via removeFileOrFolder()
         * Then the DELETE request returns either 204 (No Content)
         * Or 404 if the folder was already removed
         *
         * When the same folder path is requested via PROPFIND afterwards
         * Then the response status code is 404
         * Indicating that the folder no longer exists in WebDAV
         *
         * Finally any remaining test folder structure is removed (best effort)
         * So that no test artifacts remain in OwnCloud
         */

        $folder = ['PhpUnitFixtures', $this->uniq, 'todelete'];

        try {
            // Create it first
            $this->api->addFolderRecursive($folder);

            // Delete
            $del = $this->api->removeFileOrFolder($folder);
            $delCode = (int)array_key_first($del);

            // DELETE returns 204 No Content (or 404 if it was already gone)
            $this->assertTrue(in_array($delCode, [204, 404], true), 'DELETE should return 204 or 404, got ' . $delCode);

            // Verify missing via PROPFIND (404)
            $this->api->apiMethod = 'PROPFIND';
            $probe = $this->api->doApiRequest(WebDavApi::API_PATH . implode('/', $folder));
            $probeCode = (int)array_key_first($probe);

            $this->assertSame(404, $probeCode, 'Expected 404 after delete, got ' . $probeCode);

        } finally {
            // Cleanup whole subtree (best effort)
            try { $this->api->removeFileOrFolder(['PhpUnitFixtures', $this->uniq]); } catch (\Throwable $e) {}
        }
    }


    /**
     * Checks if the OwnCloud is active / testable
     *
     * @return void
     */
    private function skipIfOwnCloudUnavailable(): void
    {
        $baseUrl = (string)($this->api->apiBaseUrl ?? '');
        if ($baseUrl === '') {
            $this->markTestSkipped(
                'OwnCloud integration tests require a local OwnCloud instance. ' .
                'Set OWNCLOUD_URL / configure apiBaseUrl. See README of rkw_competition.'
            );
        }

        $parts = parse_url($baseUrl);
        $host = $parts['host'] ?? null;
        $scheme = $parts['scheme'] ?? 'http';
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);

        if (!$host) {
            $this->markTestSkipped(
                'OWNCLOUD_URL is invalid (' . $baseUrl . '). See README of rkw_competition.'
            );
        }

        $errno = 0;
        $errstr = '';
        $fp = @fsockopen($host, $port, $errno, $errstr, 1.5);
        if (!$fp) {
            $this->markTestSkipped(
                'OwnCloud is not reachable at ' . $host . ':' . $port . '. ' .
                'Start the OwnCloud container / check DDEV config. See README of rkw_competition.'
            );
        }
        fclose($fp);
    }
}
