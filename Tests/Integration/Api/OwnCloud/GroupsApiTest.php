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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @group integration
 */
final class GroupsApiTest extends FunctionalTestCase
{
    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/GroupsApiTest/Fixtures';

    private GroupsApi $api;
    private string $uniq;

    public static function setUpBeforeClass(): void
    {
        /*
        if (defined('SKIP_OWNCLOUD_INTEGRATION') && SKIP_OWNCLOUD_INTEGRATION) {
            self::markTestSkipped('OwnCloud integration skipped (missing ENV).');
        }
        */
    }


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

        $this->api = new GroupsApi();

        $this->skipIfOwnCloudUnavailable();
    }



    /**
     *
     * Dann kann man z.B. laufen lassen mit:
     * •    nur diese Gruppe: --group owncloud
     * •    alles außer diese: --exclude-group owncloud
     *
     * @group owncloud
     * @return void
     */
    public function testCreateListGetAndDeleteGroup(): void
    {
        /**
         * Scenario:
         *
         * Given a unique group identifier that does not yet exist in OwnCloud
         * When a group is created via the OCS provisioning API
         * Then the create call returns an array response
         *
         * When the group list is requested using a search prefix
         * Then the returned list is an array
         * Then the created group identifier is contained in the result set
         *
         * When the created group is requested directly by its identifier
         * Then the response is an array (even if no members exist yet)
         *
         * When the group is deleted
         * Then the delete call returns an array response
         */

        $groupId = $this->uniq . '_group';

        // --- Create group
        $this->api->apiMethod = 'POST';
        $this->api->queryType = 'ocs';
        $create = $this->api->doApiRequest('ocs/v1.php/cloud/groups', [
            'groupid' => $groupId,
        ]);
        $this->assertIsArray($create, 'Create group should return array');

        // --- Small wait (ownCloud can be slightly async)
        usleep(150 * 1000);

        // --- List groups (search by prefix)
        $this->api->apiMethod = 'GET';
        $list = $this->api->doApiRequest('ocs/v1.php/cloud/groups', [
            'search' => $this->uniq,
            'limit'  => 50,
            'offset' => 0,
        ]);

        // ownCloud returns a list (array of group names) in ocs->data
        $this->assertIsArray($list);
        $this->assertContains($groupId, $list['groups'], 'Created group should be in list');

        // --- Get single group (members listing by group)
        $this->api->apiMethod = 'GET';
        $get = $this->api->doApiRequest('ocs/v1.php/cloud/groups/' . rawurlencode($groupId));
        // Structure: typically a list of users; empty array is fine for a new group
        $this->assertIsArray($get);

        // --- Delete group
        $this->api->apiMethod = 'DELETE';
        $del = $this->api->doApiRequest('ocs/v1.php/cloud/groups/' . rawurlencode($groupId));
        $this->assertIsArray($del);
    }



    /**
     *
     * @group owncloud
     * @return void
     */
    public function testAddAndRemoveUserToGroup(): void
    {
        /**
         * Scenario:
         *
         * Given a unique user identifier and a unique group identifier that do not yet exist
         * When the user is created via the OCS provisioning API
         * And the group is created via the OCS provisioning API
         *
         * When the user is added to the group
         * And the groups of the user are requested
         * Then the response is an array
         * Then the created group identifier is contained in the user’s group list
         *
         * When the user is removed from the group
         * And the groups of the user are requested again
         * Then the response is an array
         * Then the group identifier is no longer contained in the user’s group list
         *
         * Finally the created group and user are deleted to clean up the test state
         */

        $groupId = $this->uniq . '_grp';
        $userId  = $this->uniq . '_usr';
        $password = 'T3st_' . substr(hash('sha1', $this->uniq), 0, 8);

        // --- Create user
        $this->api->apiMethod = 'POST';
        $this->api->queryType = 'ocs';
        $this->api->doApiRequest('ocs/v1.php/cloud/users', [
            'userid'   => $userId,
            'password' => $password,
        ]);

        // --- Create group
        $this->api->apiMethod = 'POST';
        $this->api->doApiRequest('ocs/v1.php/cloud/groups', [
            'groupid' => $groupId,
        ]);

        // --- Add user -> group
        $this->api->apiMethod = 'POST';
        $this->api->doApiRequest('ocs/v1.php/cloud/users/' . rawurlencode($userId) . '/groups', [
            'groupid' => $groupId,
        ]);

        // --- Verify membership (list groups for user)
        $this->api->apiMethod = 'GET';
        $groups = $this->api->doApiRequest('ocs/v1.php/cloud/users/' . rawurlencode($userId) . '/groups');

        $this->assertIsArray($groups);
        $this->assertContains($groupId, $groups['groups'], 'User should be in group');

        // --- Remove user from group
        $this->api->apiMethod = 'DELETE';
        $this->api->doApiRequest('ocs/v1.php/cloud/users/' . rawurlencode($userId) . '/groups', [
            'groupid' => $groupId,
        ]);

        // --- Verify removal
        $this->api->apiMethod = 'GET';
        $groups2 = $this->api->doApiRequest('ocs/v1.php/cloud/users/' . rawurlencode($userId) . '/groups');
        $this->assertIsArray($groups2);
        $this->assertNotContains($groupId, $groups2['groups'], 'User should no longer be in group');

        // --- Cleanup
        $this->api->apiMethod = 'DELETE';
        $this->api->doApiRequest('ocs/v1.php/cloud/groups/' . rawurlencode($groupId));

        $this->api->apiMethod = 'DELETE';
        $this->api->doApiRequest('ocs/v1.php/cloud/users/' . rawurlencode($userId));

        $this->assertTrue(true);
    }



    /**
     * @group owncloud
     * @return void
     */
    public function testGetGroupListBuildsCorrectOcsGetRequest(): void
    {
        /**
         * Scenario:
         *
         * Given a GroupsApi instance with a mocked HTTP client
         * And the mocked client returns a successful OCS JSON response containing one group "foo"
         *
         * When getGroupList() is called with the search parameter "foo"
         * Then the method returns the decoded OCS data array
         * And the returned value equals the "data" section of the OCS response
         *
         * When inspecting the outgoing HTTP request
         * Then the HTTP method is GET
         * And the request URI contains the groups API path
         * And the query string contains "format=json"
         * And the query string contains "search=foo"
         * And the request contains the "OCS-APIRequest: true" header
         * And the request contains an "Accept: application/json" header
         */

        $api = $this->buildApiWithMockedClient([
            new Response(200, [], json_encode([
                'ocs' => [
                    'meta' => ['status' => 'ok', 'statuscode' => 100],
                    'data' => ['groups' => ['foo']],
                ],
            ])),
        ]);

        $result = $api->getGroupList('foo');

        // Response parsing: should return ocs->data
        $this->assertIsArray($result);
        $this->assertSame(['groups' => ['foo']], $result);

        // Request assertions
        $this->assertCount(1, $this->history);
        $request = $this->history[0]['request'];

        $this->assertSame('GET', $request->getMethod());

        // URL contains format=json and search=foo
        $uri = (string)$request->getUri();
        $this->assertStringContainsString('/ocs/v1.php/cloud/groups', $uri);
        $this->assertStringContainsString('format=json', $uri);
        $this->assertStringContainsString('search=foo', $uri);

        // Headers for OCS
        $this->assertSame('true', $request->getHeaderLine('OCS-APIRequest'));
        $this->assertStringContainsString('application/json', $request->getHeaderLine('Accept'));
    }



    /**
     * @group owncloud
     * @return void
     */
    public function testAddGroupSendsPostWithUrlEncodedBodyAndFormatJsonQuery(): void
    {
        /**
         * Scenario:
         *
         * Given a GroupsApi instance with a mocked HTTP client
         * And the mocked client returns a successful OCS JSON response
         *
         * When addGroup() is called with the group name "MyGroup"
         * Then exactly one HTTP request is sent
         * And the HTTP method of the request is POST
         *
         * And the request URI contains the OCS parameter "format=json"
         * And the request body is URL-encoded
         * And the request body contains the parameter "groupid=MyGroup"
         */

        $api = $this->buildApiWithMockedClient([
            new Response(200, [], json_encode([
                'ocs' => [
                    'meta' => ['status' => 'ok', 'statuscode' => 100],
                    'data' => ['foo' => 'bar'],
                ],
            ])),
        ]);

        $api->apiMethod = 'POST';

        $api->addGroup('MyGroup');

        $this->assertCount(1, $this->history);
        $request = $this->history[0]['request'];

        $this->assertSame('POST', $request->getMethod());

        // Query must still contain format=json
        $uri = (string)$request->getUri();
        $this->assertStringContainsString('format=json', $uri);

        // Body must be urlencoded form_params: groupid=MyGroup
        $body = (string)$request->getBody();
        $this->assertSame('groupid=MyGroup', $body);
    }


    /**
     * @group owncloud
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testInvalidJsonReturnsDiagnosticArray(): void
    {
        /**
         * Scenario:
         *
         * Given an API instance with a mocked HTTP client
         * And the mocked client returns a 500 response with a non-JSON (HTML) body
         *
         * When doApiRequest() is executed for an OCS endpoint
         * Then the method returns an array instead of throwing an exception
         * And the returned array contains the original HTTP status code
         * And the returned array contains the raw response body (truncated if necessary)
         * And the returned array contains an error message indicating invalid JSON
         */

        $api = $this->buildApiWithMockedClient([
            new Response(500, ['Content-Type' => 'text/html'], '<html>oops</html>'),
        ]);

        $api->apiMethod = 'GET';

        $result = $api->doApiRequest('ocs/v1.php/cloud/groups', ['search' => 'x']);

        $this->assertIsArray($result);
        $this->assertSame(500, $result['http_code'] ?? null);
        $this->assertArrayHasKey('raw', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Invalid JSON', (string)$result['error']);
    }



    /**
     * @param array $mockResponses
     * @return GroupsApi
     */
    private function buildApiWithMockedClient(array $mockResponses): GroupsApi
    {
        $this->history = [];

        $mock = new MockHandler($mockResponses);
        $stack = HandlerStack::create($mock);

        // Capture outgoing requests + options (so we can assert query/body/headers)
        $historyMiddleware = Middleware::history($this->history);
        $stack->push($historyMiddleware);

        $client = new Client(['handler' => $stack]);

        // Force TYPO3 to use OUR mocked client when code calls GeneralUtility::makeInstance(Client::class)
        GeneralUtility::addInstance(Client::class, $client);

        $api = new GroupsApi();

        return $api;
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
