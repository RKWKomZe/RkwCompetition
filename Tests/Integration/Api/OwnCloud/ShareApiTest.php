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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @group integration
 */
final class ShareApiTest extends FunctionalTestCase
{
    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/ShareApiTest/Fixtures';

    private ShareApi $api;
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

        $this->api = new ShareApi();
        $this->api->queryType = 'ocs';

        $this->skipIfOwnCloudUnavailable();
    }



    /**
     * @group owncloud
     */
    public function testGetAllSharesDefaultReturnsArray(): void
    {
        /**
         * Scenario:
         *
         * Given a running OwnCloud instance
         * And no specific filters are provided to the shares endpoint
         *
         * When getAllShares() is called with its default parameters
         * Then the method returns an array representing the OCS "data" section
         *
         * And if shares exist
         * Then each entry in the returned list represents a share
         * And each share entry is structured as an array
         *
         * And if no shares exist
         * Then an empty array is returned
         */

        $result = $this->api->getAllShares();

        // OwnCloud OCS "getAllShares" returns a list (array) in ocs->data
        $this->assertIsArray($result);

        // Robust check: either empty array or list of shares
        // If not empty, first entry should be an array (share object)
        if (count($result) > 0) {
            $this->assertIsArray($result[0], 'Expected share entries to be arrays');
        }
    }



    /**
     * @group owncloud
     */
    public function testGetAllSharesWithFiltersDoesNotCrashAndReturnsArray(): void
    {
        /**
         * Scenario:
         *
         * Given a running OwnCloud instance
         * And explicit filter parameters for the shares endpoint
         * And the "reshares" flag is set to false
         *
         * When getAllShares() is called with these filter parameters
         * Then the method executes without throwing an exception
         * And the method returns an array representing the OCS "data" section
         *
         * And if shares are returned
         * Then each share entry in the result is structured as an array
         */

        // reshares=false should still work
        $result = $this->api->getAllShares(
            ['/'],        // path
            'json',       // format
            false,        // reshares
            '',           // sharedWithMe
            '',           // state
            false         // subfiles
        );

        $this->assertIsArray($result);

        // If OwnCloud returns entries, they should be arrays
        if (count($result) > 0) {
            $this->assertIsArray($result[0]);
        }
    }



    /**
     * @group owncloud
     */
    public function testCreateShareWithTempFolderAndCleanup(): void
    {

        /**
         * Scenario:
         *
         * Given a running OwnCloud instance
         * And a temporary unique folder inside "/PhpUnitFixtures"
         * And a file "test.txt" uploaded into that folder via WebDAV
         *
         * When a public link share (shareType 3) is created for that file via the OCS API
         * Then the response does not indicate a failure
         * And a valid share ID greater than zero is returned
         *
         * When the created share is retrieved by its ID
         * Then the response is an array
         * And the returned share ID matches the created share ID
         * And the returned share path matches the uploaded file path
         *
         * When getAllShares() is called afterwards
         * Then it returns an array (smoke test for general listing)
         *
         * Finally the created share, test file, and temporary folder are removed
         * So that no test artifacts remain in OwnCloud
         */

        // OwnCloud Pfade (im User-Home)
        $folderPath = '/PhpUnitFixtures/' . $this->uniq;
        $filePath   = $folderPath . '/test.txt';

        $shareId = null;

        try {
            // 1) Ensure base fixtures folder exists
            $this->webdavMkcol('/PhpUnitFixtures');

            // 2) Create unique test folder
            $this->webdavMkcol($folderPath);

            // 3) Upload a small file (share needs an existing file/folder)
            $this->webdavPut($filePath, "hello from {$this->uniq}\n");

            // 🔴 Wichtig: Nach WebDAV wieder auf OCS zurückschalten
            $this->api->queryType = 'ocs';
            $this->api->apiMethod = 'POST';

            // 4) Create public link share (shareType 3)
            $resp = $this->api->createShare(
                'test.txt',
                $this->pathToSegments($filePath),
                3,
                '',
                false,
                '',
                1 // read permission
            );

            // Falls OwnCloud meta failure zurückgibt
            if (isset($resp['status']) && $resp['status'] === 'failure') {
                $this->fail('Share creation failed: ' . ($resp['message'] ?? json_encode($resp)));
            }

            // Robuste Share-ID-Ermittlung
            $shareId = 0;
            if (isset($resp['id'])) {
                $shareId = (int)$resp['id'];
            } elseif (isset($resp[0]['id'])) {
                $shareId = (int)$resp[0]['id'];
            }

            $this->assertGreaterThan(
                0,
                $shareId,
                'Share ID should be returned. Response: ' . json_encode($resp)
            );

            // 🔴 Sicherstellen, dass wir im OCS-Modus bleiben
            $this->api->queryType = 'ocs';
            $this->api->apiMethod = 'GET';

            // 5) Deterministisch prüfen: Share per ID abrufen
            $share = $this->api->getShare($shareId);
            $this->assertIsArray($share);

            if (isset($share['id'])) {
                $this->assertSame($shareId, (int)$share['id'], 'Share ID should match');
                $this->assertSame($filePath, (string)($share['path'] ?? ''), 'Share path should match');
            } elseif (isset($share[0]['id'])) {
                $this->assertSame($shareId, (int)$share[0]['id'], 'Share ID should match');
            } else {
                $this->fail('Unexpected getShare response: ' . json_encode($share));
            }

            // 6) Optional: getAllShares nur als Smoke-Test (nicht deterministisch)
            $shares = $this->api->getAllShares(['/'], 'json', true, '', '', true);
            $this->assertIsArray($shares);

        } finally {
            // Cleanup should not fail the test if something is already gone
            if ($shareId) {
                try { $this->api->deleteShare($shareId); } catch (\Throwable $e) {}
            }
            try { $this->webdavDelete($filePath); } catch (\Throwable $e) {}
            try { $this->webdavDelete($folderPath); } catch (\Throwable $e) {}
            // Optional: do NOT delete /PhpUnitFixtures globally, only your unique folder
        }
    }



    /**
     * @group owncloud
     */
    public function testDeleteShareRemovesIt(): void
    {
        /**
         * Scenario:
         *
         * Given a running OwnCloud instance
         * And a temporary unique folder inside "/PhpUnitFixtures"
         * And a file "test-delete.txt" uploaded into that folder via WebDAV
         *
         * When a public link share (shareType 3) is created for that file via the OCS API
         * Then a valid share ID greater than zero is returned
         * And the share can be retrieved by its ID
         *
         * When the share is deleted via the OCS API
         * Then the delete call returns an array response
         *
         * When the deleted share is requested again by its ID
         * Then OwnCloud indicates that the share no longer exists
         * (typically via an OCS failure response with status code 404)
         *
         * Finally the test share (best effort), test file, and temporary folder are removed
         * So that no test artifacts remain in OwnCloud
         */

        $folderPath = '/PhpUnitFixtures/' . $this->uniq;
        $filePath   = $folderPath . '/test-delete.txt';

        $shareId = 0;

        try {
            // WebDAV setup
            $this->webdavMkcol('/PhpUnitFixtures');
            $this->webdavMkcol($folderPath);
            $this->webdavPut($filePath, "hello delete test {$this->uniq}\n");

            // OCS: create share
            $this->api->queryType = 'ocs';
            $this->api->apiMethod = 'POST';

            $resp = $this->api->createShare(
                'test-delete.txt',
                $this->pathToSegments($filePath),
                3,
                '',
                false,
                '',
                1
            );

            $shareId = (int)($resp['id'] ?? 0);
            $this->assertGreaterThan(0, $shareId, 'Share ID should be returned');

            // sanity: share exists
            $this->api->apiMethod = 'GET';
            $share = $this->api->getShare($shareId);
            $this->assertIsArray($share);

            // delete share
            $this->api->apiMethod = 'DELETE';
            $del = $this->api->deleteShare($shareId);
            $this->assertIsArray($del);

            // verify: share is gone (getShare should return failure meta OR something without id)
            $this->api->apiMethod = 'GET';
            $gone = $this->api->getShare($shareId);

            // dein doApiRequest() gibt bei OCS failure meta zurück
            if (isset($gone['status']) && $gone['status'] === 'failure') {
                // ownCloud typically: 404 when share doesn't exist
                $statusCode = (int)($gone['statuscode'] ?? 0);
                $this->assertTrue(in_array($statusCode, [404, 997], true), 'Expected 404 (or 997 unauthorised) after delete. Got: ' . json_encode($gone));
            } else {
                // falls doch data kommt, darf es nicht mehr die ID enthalten
                $id = (int)($gone['id'] ?? ($gone[0]['id'] ?? 0));
                $this->assertNotSame($shareId, $id, 'Deleted share should not be retrievable anymore');
            }
        } finally {
            // best-effort cleanup
            if ($shareId > 0) {
                try {
                    $this->api->queryType = 'ocs';
                    $this->api->apiMethod = 'DELETE';
                    $this->api->deleteShare($shareId);
                } catch (\Throwable $e) {}
            }
            try { $this->webdavDelete($filePath); } catch (\Throwable $e) {}
            try { $this->webdavDelete($folderPath); } catch (\Throwable $e) {}
        }
    }



    /**
     * @group owncloud
     */
    public function testCreatePublicLinkShareWithPassword(): void
    {
        /**
         * Scenario:
         *
         * Given a running OwnCloud instance
         * And a temporary unique folder inside "/PhpUnitFixtures"
         * And a file "test-pw.txt" uploaded into that folder via WebDAV
         * And a randomly generated password for a public link share
         *
         * When a public link share (shareType 3) is created for that file via the OCS API
         * And the share is protected with the generated password
         * Then the response does not indicate a failure
         * And a valid share ID greater than zero is returned
         *
         * When the created share is retrieved by its ID
         * Then the response is an array
         * And the returned share ID matches the created share ID
         * And the returned share path matches the uploaded file path
         * And the returned share type indicates a public link share
         *
         * Then the share password is not leaked in plaintext in the response payload
         * And if OwnCloud exposes a "password_protected" flag
         * Then the share is marked as password protected
         *
         * Finally the created share, test file, and temporary folder are removed
         * So that no test artifacts remain in OwnCloud
         */

        $folderPath = '/PhpUnitFixtures/' . $this->uniq;
        $filePath   = $folderPath . '/test-pw.txt';

        $shareId = 0;
        $password = 'T3st_' . substr(hash('sha1', $this->uniq), 0, 10);

        try {
            // WebDAV setup
            $this->webdavMkcol('/PhpUnitFixtures');
            $this->webdavMkcol($folderPath);
            $this->webdavPut($filePath, "hello pw test {$this->uniq}\n");

            // OCS: create share with password
            $this->api->queryType = 'ocs';
            $this->api->apiMethod = 'POST';

            $resp = $this->api->createShare(
                'test-pw.txt',
                $this->pathToSegments($filePath),
                3,          // public link
                '',         // shareWith empty for public link
                false,      // publicUpload
                $password,  // password
                1           // read
            );

            // Failure handling (depending on your doApiRequest contract)
            if (isset($resp['status']) && $resp['status'] === 'failure') {
                $this->fail('Share creation failed: ' . ($resp['message'] ?? json_encode($resp)));
            }

            $shareId = (int)($resp['id'] ?? 0);
            $this->assertGreaterThan(0, $shareId, 'Share ID should be returned. Response: ' . json_encode($resp));

            // Get share by ID and verify basic fields
            $this->api->apiMethod = 'GET';
            $share = $this->api->getShare($shareId);
            $this->assertIsArray($share);

            // Response can be either share-array or list; normalize
            $shareData = $share;
            if (!isset($shareData['id']) && isset($shareData[0]) && is_array($shareData[0])) {
                $shareData = $shareData[0];
            }

            $this->assertSame($shareId, (int)($shareData['id'] ?? 0), 'Share ID should match');
            $this->assertSame($filePath, (string)($shareData['path'] ?? ''), 'Share path should match');
            $this->assertSame(3, (int)($shareData['share_type'] ?? 0), 'Share type should be public link');

            // Password expectations:
            // OwnCloud typically does NOT return the plaintext password.
            // Depending on version/config it may return a flag like "password" => true/1, or it may omit it.
            // So we test "password is NOT leaked" and (optionally) that it indicates protection.
            if (isset($shareData['password'])) {
                // ensure it's not the actual password
                $this->assertNotSame($password, (string)$shareData['password'], 'Password must not be returned in plaintext');
            }

            // Optional: if OwnCloud provides a password flag, it should indicate protection.
            // Some setups return "password" as empty string even though it is protected -> don't hard-fail.
            if (isset($shareData['password_protected'])) {
                $this->assertTrue((bool)$shareData['password_protected'], 'Share should be marked as password protected');
            }

        } finally {
            // cleanup: delete share, then webdav content
            if ($shareId > 0) {
                try {
                    $this->api->queryType = 'ocs';
                    $this->api->apiMethod = 'DELETE';
                    $this->api->deleteShare($shareId);
                } catch (\Throwable $e) {}
            }

            try { $this->webdavDelete($filePath); } catch (\Throwable $e) {}
            try { $this->webdavDelete($folderPath); } catch (\Throwable $e) {}
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



    /**
     * @param string $ocPath
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function webdavMkcol(string $ocPath): void
    {
        $this->api->queryType  = 'webdav';
        $this->api->apiMethod  = 'MKCOL';

        // Wichtig: damit doApiRequest() es als WebDAV erkennt: remote.php/webdav im Pfad
        $res = $this->api->doApiRequest('remote.php/webdav' . $ocPath);

        // WebDAV: doApiRequest() liefert [httpCode => body]
        $code = (int)array_key_first($res);

        // 201 Created ist ok; 405 Method Not Allowed kommt häufig wenn Ordner schon existiert
        if (!in_array($code, [201, 405], true)) {
            $this->fail('MKCOL failed for ' . $ocPath . ' with HTTP ' . $code);
        }
    }



    /**
     * @param string $ocPath
     * @param string $content
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function webdavPut(string $ocPath, string $content): void
    {
        $this->api->queryType = 'webdav';
        $this->api->apiMethod = 'PUT';

        // doApiRequest() unterstützt aktuell nur form_params/query – für PUT Body fehlt dir noch was.
        // Daher: Quick&dirty über PHP stream wrapper geht hier nicht.
        // Besser: erweitere doApiRequest um 'body' Option ODER nutze direkt Guzzle im Test.

        // => Minimaler Weg: direkt Guzzle im Test (sauberer):
        $base = rtrim((string)$this->api->apiBaseUrl, '/') . '/';
        $url  = $base . ltrim('remote.php/webdav' . $ocPath, '/');

        $client = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\GuzzleHttp\Client::class);
        $resp = $client->request('PUT', $url, [
            'auth'        => [$this->api->apiAdminUsername, $this->api->apiAdminPassword],
            'timeout'     => 5,
            'http_errors' => false,
            'body'        => $content,
        ]);

        $this->assertTrue(in_array($resp->getStatusCode(), [200, 201, 204], true), 'PUT failed with HTTP ' . $resp->getStatusCode());
    }



    /**
     * @param string $ocPath
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function webdavDelete(string $ocPath): void
    {
        $this->api->queryType = 'webdav';
        $this->api->apiMethod = 'DELETE';

        $res = $this->api->doApiRequest('remote.php/webdav' . $ocPath);
        $code = (int)array_key_first($res);

        // 204 No Content ok; 404 ok für Cleanup
        if (!in_array($code, [204, 404], true)) {
            $this->fail('DELETE failed for ' . $ocPath . ' with HTTP ' . $code);
        }
    }



    /**
     * @param string $ocPath
     * @return string[]
     */
    private function pathToSegments(string $ocPath): array
    {
        // ShareApi erwartet array $path und macht implode('/')
        // Wichtig: keine leading/trailing slashes als eigene Segmente
        $trim = trim($ocPath, '/');
        return $trim === '' ? ['/'] : explode('/', $trim);
    }

}
