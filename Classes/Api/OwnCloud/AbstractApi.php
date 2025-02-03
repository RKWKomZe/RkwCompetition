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

use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use Madj2k\CoreExtended\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class AbstractApi
 *
 * config/config.php
 *
 * App: "rkw-test"; User: "admin", PW: "MAGVN-WYKFW-IPJAB-ILMIP"
 *
 * https://doc.owncloud.com/server/next/developer_manual/core/apis/ocs-user-sync-api.html
 *
 *
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
    protected string $apiBaseUrl = '';

    /**
     * @var string
     */
    protected string $apiAdminUsername = '';


    /**
     * @var string
     */
    protected string $apiAdminPassword = '';


    /**
     * @var resource A stream context resource
     */
    protected $streamContext = null;

    /**
     * @var string Example options: "DELETE", "GET", "POST" (and more)
     */
    protected string $apiMethod = '';

    /**
     * webDav queries have a different response (XML). To handle customized stuff you can work with "$queryType"
     *
     * @var string
     */
    protected string $queryType = '';

    /**
     * @var \TYPO3\CMS\Core\Log\Logger|null
     */
    protected ?Logger $logger = null;


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

        $this->streamContext = stream_context_create($opts);
    }




    /**
     * Makes the api request
     *
     * @param string $targetPath The action ("users", "groups", "apps"); can be also more particular like "/users/Frank"
     * @param array $arguments
     * @return array
     */
    protected function doApiRequest(string $targetPath, array $arguments = []): array
    {
        // &search=Hannes
        $url = $this->apiBaseUrl . $targetPath . '?format=json';

        DebuggerUtility::var_dump($arguments);
        DebuggerUtility::var_dump($url);


        if (
            $this->apiMethod === self::METHOD_GET
            && $arguments
        ) {
            $url .= '&' . http_build_query($arguments);
        }


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER,CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiAdminUsername . ':' . $this->apiAdminPassword);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        // CURLOPT_CUSTOMREQUEST is used below
        //curl_setopt($ch, CURLOPT_POST, true);
        if (
            $arguments
            && $this->apiMethod != self::METHOD_GET
        ) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arguments));
        }

   //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arguments));

//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_USERPWD, $this->apiAdminUsername . ':' . $this->apiAdminPassword);  // Set credentials

        // Set the request method (to realize "DELETE" e.g.))
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->apiMethod);
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        // if it's webdav stuff: Return HTTP-code and response message (XML)
        if ($this->queryType === 'webdav') {
            return [$http_code => $response];
        }


        // Handle any potential errors
        if(curl_error($ch)) {
            DebuggerUtility::var_dump('Error:' . curl_error($ch));

            exit;
        }

        $data = json_decode(curl_exec($ch));

        $array = json_decode((string) json_encode($data), true);

        curl_close($ch);

        DebuggerUtility::var_dump($array); exit;

        // @toDo: Question: Return the part we need, or just return all?

        if ($array['ocs']['meta']['status'] == "failure") {
            return $array['ocs']['meta'];
        }
        return $array['ocs']['data'];

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

    /**
     * Returns specific request path ("users" e.g.)
     *
     * @deprecated
     *
     * @param string $which Which type of request-URL is needed
     * @return string
     */
    protected function getRequestPath(string $which): string
    {
        return $this->apiBaseUrl . 'ocs/v1.php/cloud/' . $which;
    }
}
