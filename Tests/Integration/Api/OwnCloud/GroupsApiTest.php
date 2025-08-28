<?php
declare(strict_types=1);

namespace RKW\RkwCompetition\Tests\Integration\Api\OwnCloud;

use PHPUnit\Framework\TestCase;
use RKW\RkwCompetition\Api\OwnCloud\GroupsApi;

/**
 * @group integration
 */
final class GroupsApiTest extends TestCase
{
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

    protected function setUp(): void
    {
        $this->uniq = 'PhpUnit_' . gmdate('Ymd_His') . '_' . substr(bin2hex(random_bytes(3)), 0, 6);

        $this->api = new GroupsApi();
        // Die Properties so setzen, wie es deine AbstractApi vorsieht:
        $this->api->apiBaseUrl       = rtrim(getenv('OWNCLOUD_URL'), '/');
        $this->api->apiAdminUsername = getenv('OWNCLOUD_ADMIN_USER');
        $this->api->apiAdminPassword = getenv('OWNCLOUD_ADMIN_PASS');
    }

    public function testCreateListGetAndDeleteGroup(): void
    {
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
        $this->assertContains($groupId, (array)$list, 'Created group should be in list');

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

    public function testAddAndRemoveUserToGroup(): void
    {
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
        $this->assertContains($groupId, (array)$groups, 'User should be in group');

        // --- Remove user from group
        $this->api->apiMethod = 'DELETE';
        $this->api->doApiRequest('ocs/v1.php/cloud/users/' . rawurlencode($userId) . '/groups', [
            'groupid' => $groupId,
        ]);

        // --- Verify removal
        $this->api->apiMethod = 'GET';
        $groups2 = $this->api->doApiRequest('ocs/v1.php/cloud/users/' . rawurlencode($userId) . '/groups');
        $this->assertIsArray($groups2);
        $this->assertNotContains($groupId, (array)$groups2, 'User should no longer be in group');

        // --- Cleanup
        $this->api->apiMethod = 'DELETE';
        $this->api->doApiRequest('ocs/v1.php/cloud/groups/' . rawurlencode($groupId));

        $this->api->apiMethod = 'DELETE';
        $this->api->doApiRequest('ocs/v1.php/cloud/users/' . rawurlencode($userId));

        $this->assertTrue(true);
    }
}
