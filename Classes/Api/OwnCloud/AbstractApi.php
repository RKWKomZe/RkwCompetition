<?php
namespace RKW\RkwCompetition\Api\OwnCloud;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use GuzzleHttp\Client;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use Madj2k\CoreExtended\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class AbstractApi
 *
 * https://doc.owncloud.com/server/next/developer_manual/core/apis/provisioning-api.html
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 * @copyright RKW Kompetenzzentrum
 * @package RKW_Competition
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class AbstractApi implements \TYPO3\CMS\Core\SingletonInterface
{

    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_MKCOL = 'MKCOL';
    const METHOD_PROPFIND = 'PROPFIND';

    /**
     * @var string
     */
    public string $apiBaseUrl = '';

    /**
     * @var string
     */
    public string $apiAdminUsername = '';


    /**
     * @var string
     */
    public string $apiAdminPassword = '';


    /**
     * @var resource A stream context resource
     */
    protected $streamContext = null;

    /**
     * @var string Example options: "DELETE", "GET", "POST" (and other)
     */
    public string $apiMethod = '';

    /**
     * webDav queries have a different response (XML). To handle customized stuff you can work with "$queryType"
     *
     * @var string
     */
    public string $queryType = '';

    /**
     * @var \TYPO3\CMS\Core\Log\Logger|null
     */
    protected ?Logger $logger = null;

    //protected Client $client;


    /**
     * initializeObject
     *
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function __construct()
    {
        $settingsDefault = $this->getSettings();

        // Set the private API key for the user (from the user account page) and the user we're accessing the system as.
        $this->apiBaseUrl = filter_var($settingsDefault['api']['ownCloud']['baseUrl'], FILTER_SANITIZE_STRING);
        $this->apiAdminUsername = filter_var($settingsDefault['api']['ownCloud']['admin']['username'], FILTER_SANITIZE_STRING);
        $this->apiAdminPassword = filter_var($settingsDefault['api']['ownCloud']['admin']['password'], FILTER_SANITIZE_STRING);

        $this->apiMethod = self::METHOD_POST;

        /*
        $this->client = $client ?? new Client([
            'base_uri'        => rtrim($this->apiBaseUrl, '/') . '/',
            'timeout'         => 5.0,
            'connect_timeout' => 5.0,
            'auth'            => [$this->apiAdminUsername, $this->apiAdminPassword],
            'headers'         => [
                'Accept'     => 'application/json',
                'User-Agent' => 'AbstractApi/1.0',
            ],
        ]);
        */


        // @toDo: We need more than one header type. Not used
        // login header
        $opts = array(
            'http' => array(
                'method' => 'GET',
            ),
        );


        // @toDo: Is proxy needed? (ask SK)

        // optional: proxy configuration
        if ($settingsDefault['ownCloud']['proxy']) {

            $optsProxy = array(
                'http' => array(
                    'proxy'           => $settingsDefault['ownCloud']['proxy'],
                    'request_fulluri' => true,
                ),
            );

            if ($settingsDefault['ownCloud']['proxyUsername']) {
                $auth = base64_encode(
                    $settingsDefault['ownCloud']['proxyUsername'] . ':'
                    . $settingsDefault['ownCloud']['proxyPassword']
                );
                $optsProxy['http']['header'] = 'Proxy-Authorization: Basic ' . $auth;
            }
            $opts = array_merge_recursive($opts, $optsProxy);
        }

        // The streamcontext is not used
        $this->streamContext = stream_context_create($opts);
    }




    /**
     * Makes the api request
     *
     * @param string $targetPath The action ("users", "groups", "apps"); can be also more particular like "/users/Frank"
     * @param array $arguments
     * @return array
     */
    public function doApiRequest(string $targetPath, array $arguments = []): array
    {

        $isWebdav = ($this->queryType === 'webdav') || str_contains($targetPath, 'remote.php/webdav');
        $method   = strtoupper($this->apiMethod ?? 'GET');

        // Build full URL exactly like with cURL
        $base = rtrim($this->apiBaseUrl, '/') . '/';
        $url  = $base . ltrim($targetPath, '/');

        // For OCS (non-WebDAV) always ensure ?format=json like your cURL code
        $query = [];
        $headers = [];
        if (!$isWebdav) {
            // OCS needs this for JSON/CSRF-safe responses
            $headers['OCS-APIRequest'] = 'true';
            $headers['Accept']         = 'application/json';
            $query['format']           = 'json';
        }

        // GET → put $arguments into query; others → urlencoded body
        $options = [
            'auth'            => [$this->apiAdminUsername, $this->apiAdminPassword],
            'timeout'         => 5,
            'connect_timeout' => 5,
            'http_errors'     => false,   // behave like your cURL: don't throw on 4xx/5xx
            'headers'         => $headers,
        ];

        if ($method === 'GET') {
            $options['query'] = $query + $arguments;
        } else {
            $options['query'] = $query;
            if (!empty($arguments)) {
                // urlencoded, just like http_build_query in cURL
                $options['form_params'] = $arguments;
            }
        }

        // Fire request (no base_uri — we pass the full URL)
        $client   = GeneralUtility::makeInstance(Client::class);
        $response = $client->request($method, $url, $options);

        $httpCode = $response->getStatusCode();
        $body     = (string)$response->getBody();

        // Keep your WebDAV behavior: return [code => XML/response]
        if ($isWebdav) {
            return [$httpCode => $body];
        }

        // For OCS JSON: mirror your previous parsing
        $decoded = json_decode($body, true);

        // If JSON decoding fails (e.g., HTML error), return a compact diagnostic like cURL-debug would give
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'http_code' => $httpCode,
                'raw'       => mb_substr($body, 0, 2000),
                'error'     => 'Invalid JSON: ' . json_last_error_msg(),
            ];
        }

        // Keep your old meta/data contract
        if (($decoded['ocs']['meta']['status'] ?? null) === 'failure') {
            return $decoded['ocs']['meta'];
        }

        // Prefer data, otherwise fall back to the whole decoded payload
        return $decoded['ocs']['data'] ?? $decoded;
    }



    /**
     * Returns TYPO3 settings
     *
     * @param string $which Which type of settings will be loaded
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected static function getSettings(string $which = ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS): array
    {
        return GeneralUtility::getTypoScriptConfiguration('Rkwcompetition', $which);
    }


    /**
     * Returns logger instance
     *
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger(): Logger
    {
        if (!$this->logger instanceof \TYPO3\CMS\Core\Log\Logger) {
            $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        }

        return $this->logger;
    }

}
