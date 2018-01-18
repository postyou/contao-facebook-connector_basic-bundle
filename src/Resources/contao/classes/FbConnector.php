<?php

/**
 *
 * Extension for Contao Open Source CMS (contao.org)
 *
 * Copyright (c) 2016-2018 POSTYOU
 *
 * @package
 * @author  Mario Gienapp
 * @link    http://www.postyou.de
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Postyou\ContaoFacebookConnectorBasicBundle;


class FbConnector
{
    protected $arrConfig = array();

    protected static $arrInstances = array();

    protected $accesstoken;

    protected $limit = null;

    protected $since = null;

    protected function __construct(array $arrConfig)
    {
        $this->arrConfig = $arrConfig;
        $this->initFacebookPhpSdk();
        \System::loadLanguageFile('tl_facebook_sites');
    }

    public static function getInstance(array $arrCustomOpt = null)
    {
        $arrConfig = array(
            'baseUrl' => 'https://graph.facebook.com',
            'version' => \Config::get('facebookApiVersion'),
            'appID' => \Config::get('appID'),
            'appSecret' => \Config::get('appSecret'),
            'connectionType' => ConnectionType::POST_GET
        );

        if (is_array($arrCustomOpt)) {
            $arrConfig = array_merge($arrConfig, $arrCustomOpt);
        }

        ksort($arrConfig);
        $strKey = md5(implode(' ', $arrConfig));

        if (! isset(static::$arrInstances[$strKey])) {
            $namespace = '';
            if ($arrConfig['connectionType'] === ConnectionType::POST_GET) {
                $namespace = 'Postyou\ContaoFacebookConnectorBasicBundle';
            } else {
                $namespace = 'Postyou\ContaoFacebookConnectorProBundle';
            }

            $strClass = $namespace . '\FbConnector' .
                 str_replace(' ', '_', ucwords(str_replace('_', ' ', $arrConfig['connectionType'])));

            static::$arrInstances[$strKey] = new $strClass($arrConfig);
        }
        return static::$arrInstances[$strKey];
    }

    protected function getAppID()
    {
        if (empty($this->arrConfig['appID'])) {
            throw new \Exception($GLOBALS['TL_LANG']['tl_facebook_sites']['noAppIDException']);
        }
        return $this->arrConfig['appID'];
    }

    protected function getAppSecret()
    {
        if (empty($this->arrConfig['appSecret'])) {
            throw new \Exception($GLOBALS['TL_LANG']['tl_facebook_sites']['noAppSecretException']);
        }
        return $this->arrConfig['appSecret'];
    }

    public function getBaseUrl()
    {
        return $this->arrConfig['baseUrl'];
    }

    public function getVersion()
    {
        return $this->arrConfig['version'];
    }

    public function getAccessTokenQuery()
    {
        try {
            return 'access_token=' . $this->getAppID() . '|' . $this->getAppSecret();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function setSince($timestamp)
    {
        $this->since = $timestamp;
    }

    protected function fetchData($url, $header = false)
    {
        $ch = $this->initCurl($url, $header);
        return json_decode(curl_exec($ch), true);
    }

    protected function initCurl($url, $header = false)
    {
        $ch = curl_init($url);
        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        return $ch;
    }

    protected function initFacebookPhpSdk()
    {
        return;
    }
}
