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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @group integration
 */
final class UsersApiTest extends FunctionalTestCase
{
    /**
     * @const
     */
    const FIXTURE_PATH = __DIR__ . '/UsersApiTest/Fixtures';

    private UsersApi $usersApi;
    private GroupsApi $groupsApi;
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



        $this->usersApi = new UsersApi();
        $this->usersApi->queryType = 'ocs';

        $this->groupsApi = new GroupsApi();
        $this->groupsApi->queryType = 'ocs';

        $this->skipIfOwnCloudUnavailable();

    }



    /**
     * @group owncloud
     */
    public function testCreateGetAndDeleteUser(): void
    {
        /**
         * Scenario:
         *
         * Given a unique user identifier that does not yet exist in OwnCloud
         * And a valid password for that user
         *
         * When the user is created via the OCS provisioning API
         * Then the create call does not return an OCS failure response
         * And the create call returns an array response
         *
         * When the created user is requested directly by its identifier
         * Then the response is an array
         * And the returned user data is not empty
         *
         * Finally the created user is deleted (best effort)
         * So that no test artifacts remain in OwnCloud
         */

        $userId = $this->uniq . '_usr';
        $password = 'T3st_' . substr(hash('sha1', $this->uniq), 0, 10);

        try {
            // Create user
            $this->usersApi->apiMethod = 'POST';
            $create = $this->usersApi->addUser($userId, $password);

            if (isset($create['status']) && $create['status'] === 'failure') {
                $this->fail('addUser failed: ' . ($create['message'] ?? json_encode($create)));
            }
            $this->assertIsArray($create);

            // Get user
            $this->usersApi->apiMethod = 'GET';
            $user = $this->usersApi->getUser($userId);
            $this->assertIsArray($user);

            // OwnCloud provisioning API user output can vary; minimally ensure we got something non-empty
            $this->assertNotEmpty($user, 'getUser should return user data');

        } finally {
            // Cleanup: delete user (best effort)
            try {
                $this->usersApi->queryType = 'ocs';
                $this->usersApi->apiMethod = 'DELETE';
                $this->usersApi->deleteUser($userId);
            } catch (\Throwable $e) {}
        }
    }


    /**
     * @group owncloud
     */
    public function testDisableAndEnableUser(): void
    {
        /**
         * Scenario:
         *
         * Given a unique user identifier that does not yet exist in OwnCloud
         * And a valid password for that user
         *
         * When the user is created via the OCS provisioning API
         * Then the create call does not return an OCS failure response
         *
         * When the user is disabled via the OCS provisioning API
         * Then the disable call does not return an OCS failure response
         *
         * When the user is enabled again via the OCS provisioning API
         * Then the enable call does not return an OCS failure response
         *
         * When the user is requested afterwards
         * Then the response is an array
         * And the returned user data is not empty (smoke test)
         *
         * Finally the created user is deleted (best effort)
         * So that no test artifacts remain in OwnCloud
         */

        $userId = $this->uniq . '_usr2';
        $password = 'T3st_' . substr(hash('sha1', $this->uniq), 0, 10);

        try {
            // Create user
            $this->usersApi->apiMethod = 'POST';
            $create = $this->usersApi->addUser($userId, $password);
            if (isset($create['status']) && $create['status'] === 'failure') {
                $this->fail('addUser failed: ' . ($create['message'] ?? json_encode($create)));
            }

            // Disable
            $this->usersApi->apiMethod = 'PUT';
            $disable = $this->usersApi->disableUser($userId);
            if (isset($disable['status']) && $disable['status'] === 'failure') {
                $this->fail('disableUser failed: ' . ($disable['message'] ?? json_encode($disable)));
            }

            // Enable
            $this->usersApi->apiMethod = 'PUT';
            $enable = $this->usersApi->enableUser($userId);
            if (isset($enable['status']) && $enable['status'] === 'failure') {
                $this->fail('enableUser failed: ' . ($enable['message'] ?? json_encode($enable)));
            }

            // Get user (smoke)
            $this->usersApi->apiMethod = 'GET';
            $user = $this->usersApi->getUser($userId);
            $this->assertIsArray($user);
            $this->assertNotEmpty($user);

        } finally {
            try {
                $this->usersApi->apiMethod = 'DELETE';
                $this->usersApi->deleteUser($userId);
            } catch (\Throwable $e) {}
        }
    }


    /**
     * @group owncloud
     */
    public function testAddAndRemoveUserToGroupViaUsersApi(): void
    {
        /**
         * Scenario:
         *
         * Given a unique user identifier and a unique group identifier that do not yet exist in OwnCloud
         * And a valid password for the user
         *
         * When the user is created via the OCS provisioning API
         * And the group is created via the OCS provisioning API
         * Then neither creation call returns an OCS failure response
         *
         * When the user is added to the group via the Users API
         * And the groups of the user are requested
         * Then the response is an array
         * And the returned group list contains the created group identifier
         *
         * When the user is removed from the group via the Users API
         * And the groups of the user are requested again
         * Then the response is an array
         * And the returned group list no longer contains the group identifier
         *
         * Finally the created group and user are deleted (best effort)
         * So that no test artifacts remain in OwnCloud
         */

        $userId = $this->uniq . '_usr3';
        $groupId = $this->uniq . '_grp3';
        $password = 'T3st_' . substr(hash('sha1', $this->uniq), 0, 10);

        try {
            // Create user
            $this->usersApi->apiMethod = 'POST';
            $createUser = $this->usersApi->addUser($userId, $password);
            if (isset($createUser['status']) && $createUser['status'] === 'failure') {
                $this->fail('addUser failed: ' . ($createUser['message'] ?? json_encode($createUser)));
            }

            // Create group
            $this->groupsApi->apiMethod = 'POST';
            $createGroup = $this->groupsApi->addGroup($groupId);
            if (isset($createGroup['status']) && $createGroup['status'] === 'failure') {
                $this->fail('addGroup failed: ' . ($createGroup['message'] ?? json_encode($createGroup)));
            }

            // Add user to group
            $this->usersApi->apiMethod = 'POST';
            $add = $this->usersApi->addToGroup($userId, $groupId);
            if (isset($add['status']) && $add['status'] === 'failure') {
                $this->fail('addToGroup failed: ' . ($add['message'] ?? json_encode($add)));
            }

            // Verify membership
            $this->usersApi->apiMethod = 'GET';
            $groups = $this->usersApi->getGroups($userId);
            $this->assertIsArray($groups);

            // provisioning API usually returns ['groups' => [...]]
            if (isset($groups['groups']) && is_array($groups['groups'])) {
                $this->assertContains($groupId, $groups['groups'], 'User should be in group');
            } else {
                $this->fail('Unexpected getGroups response: ' . json_encode($groups));
            }

            // Remove from group
            $this->usersApi->apiMethod = 'DELETE';
            $rm = $this->usersApi->removeFromGroup($userId, $groupId);
            if (isset($rm['status']) && $rm['status'] === 'failure') {
                $this->fail('removeFromGroup failed: ' . ($rm['message'] ?? json_encode($rm)));
            }

            // Verify removal
            $this->usersApi->apiMethod = 'GET';
            $groups2 = $this->usersApi->getGroups($userId);
            $this->assertIsArray($groups2);

            if (isset($groups2['groups']) && is_array($groups2['groups'])) {
                $this->assertNotContains($groupId, $groups2['groups'], 'User should no longer be in group');
            } else {
                $this->fail('Unexpected getGroups response: ' . json_encode($groups2));
            }

        } finally {
            // Cleanup: group then user
            try {
                $this->groupsApi->apiMethod = 'DELETE';
                $this->groupsApi->doApiRequest(GroupsApi::API_PATH . '/' . rawurlencode($groupId));
            } catch (\Throwable $e) {}

            try {
                $this->usersApi->apiMethod = 'DELETE';
                $this->usersApi->deleteUser($userId);
            } catch (\Throwable $e) {}
        }
    }


    /**
     * @group owncloud
     */
    public function testAddUserTwiceShouldFail(): void
    {
        /**
         * Scenario:
         *
         * Given a unique user identifier that does not yet exist in OwnCloud
         * And a valid password for that user
         *
         * When the user is created via the OCS provisioning API
         * Then the create call does not return an OCS failure response
         *
         * When the same user is created again with the same identifier
         * Then the second call returns an array response indicating an OCS failure
         * And the returned status code equals 102 (username already exists)
         *
         * Finally the created user is deleted (best effort)
         * So that no test artifacts remain in OwnCloud
         */

        $userId = $this->uniq . '_dup';
        $password = 'T3st_' . substr(hash('sha1', $this->uniq), 0, 10);

        try {
            // First creation
            $this->usersApi->queryType = 'ocs';
            $this->usersApi->apiMethod = 'POST';
            $first = $this->usersApi->addUser($userId, $password);

            if (isset($first['status']) && $first['status'] === 'failure') {
                $this->fail('Initial addUser failed unexpectedly: ' . json_encode($first));
            }

            // Second creation (should fail with statuscode 102)
            $second = $this->usersApi->addUser($userId, $password);

            $this->assertIsArray($second);
            $this->assertSame('failure', $second['status'] ?? null, 'Second addUser should fail');

            $statusCode = (int)($second['statuscode'] ?? 0);
            $this->assertSame(102, $statusCode, 'Expected statuscode 102 for duplicate user');

        } finally {
            try {
                $this->usersApi->apiMethod = 'DELETE';
                $this->usersApi->deleteUser($userId);
            } catch (\Throwable $e) {}
        }
    }


    /**
     * @group owncloud
     */
    public function testEditUserDisplayName(): void
    {
        /**
         * Scenario:
         *
         * Given a unique user identifier that does not yet exist in OwnCloud
         * And a valid password for that user
         * And a new display name value
         *
         * When the user is created via the OCS provisioning API
         * Then the create call does not return an OCS failure response
         *
         * When the user's "displayname" field is updated via the OCS provisioning API
         * Then the edit call does not return an OCS failure response
         *
         * When the user is requested afterwards
         * Then the response is an array
         * And the returned display name matches the updated value
         *
         * Finally the created user is deleted (best effort)
         * So that no test artifacts remain in OwnCloud
         */

        $userId = $this->uniq . '_edit';
        $password = 'T3st_' . substr(hash('sha1', $this->uniq), 0, 10);
        $newDisplayName = 'PHPUnit ' . $this->uniq;

        try {
            // Create user
            $this->usersApi->queryType = 'ocs';
            $this->usersApi->apiMethod = 'POST';
            $create = $this->usersApi->addUser($userId, $password);

            if (isset($create['status']) && $create['status'] === 'failure') {
                $this->fail('addUser failed: ' . json_encode($create));
            }

            // Edit displayname
            $this->usersApi->apiMethod = 'PUT';
            $edit = $this->usersApi->editUser($userId, 'displayname', $newDisplayName);

            if (isset($edit['status']) && $edit['status'] === 'failure') {
                $this->fail('editUser failed: ' . json_encode($edit));
            }

            // Verify via getUser
            $this->usersApi->apiMethod = 'GET';
            $user = $this->usersApi->getUser($userId);
            $this->assertIsArray($user);

            // Depending on response shape:
            $data = $user;
            if (!isset($data['displayname']) && isset($data[0])) {
                $data = $data[0];
            }

            $this->assertSame(
                $newDisplayName,
                (string)($data['displayname'] ?? ''),
                'Displayname should have been updated'
            );

        } finally {
            try {
                $this->usersApi->apiMethod = 'DELETE';
                $this->usersApi->deleteUser($userId);
            } catch (\Throwable $e) {}
        }
    }


    /**
     * Checks if the OwnCloud is active / testable
     *
     * @return void
     */
    private function skipIfOwnCloudUnavailable(): void
    {
        $baseUrl = (string)($this->usersApi->apiBaseUrl ?? '');
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
