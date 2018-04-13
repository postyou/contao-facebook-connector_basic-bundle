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


use Symfony\Component\HttpFoundation\JsonResponse;
use Contao\CoreBundle\Exception\ResponseException;
use Symfony\Component\HttpFoundation\Response;
use Contao\System;

class BackendAjaxHook extends \Backend
{

    protected function loadData($connectionType, $functionName) {
        try {

            $id = \Input::get('id');
            $fbConnector = FbConnector::getInstance(
                array(
                    'connectionType' => $connectionType
                ));

            $fbConnector->$functionName($id);

        } catch (\Throwable $e) {
            \System::loadLanguagefile('tl_facebook_sites');
            throw new ResponseException(new JsonResponse(array('exception' => $e->getMessage(), 'title' => $GLOBALS['TL_LANG']['tl_facebook_sites']['authenticationExceptionTitle']), 500));
        } catch (\Exception $e) {
            \System::loadLanguagefile('tl_facebook_sites');
            throw new ResponseException(new JsonResponse(array('exception' => $e->getMessage(), 'title' => $GLOBALS['TL_LANG']['tl_facebook_sites']['authenticationExceptionTitle']), 500));
        } catch (\Error $e) {
            \System::loadLanguagefile('tl_facebook_sites');
            throw new ResponseException(new JsonResponse(array('exception' => $e->getMessage(), 'title' => $GLOBALS['TL_LANG']['tl_facebook_sites']['authenticationExceptionTitle']), 500));
        }
    }

    public function executePreActions($strAction)
    {
        switch ($strAction) {
            case 'loadPosts':
                if (! FbConnectorHelper::isFacebookSitesSession()) {
                    $_SESSION['tl_facebook_sites'] = array();
                }

                $_SESSION['tl_facebook_sites']['savedPosts'] = array();
                $_SESSION['tl_facebook_sites']['savedPostsCount'] = 0;

                $this->loadData(ConnectionType::POST_GET, "getPostsFromSiteIdAndSaveInDb");

                $url = \Controller::addToUrl('&reportTypePost=true&directionGet=true&rt='.FbConnectorHelper::getToken(), true,
                    array(
                        'key',
                        'directionPublish',
                        'reportTypeEvent',
                        'reportTypeGallery'
                    ));


                throw new ResponseException(new JsonResponse(html_entity_decode($url), 200));

                break;

            default:
                break;
        }
    }
}
