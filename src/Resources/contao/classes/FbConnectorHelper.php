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

use Contao\System;

class FbConnectorHelper
{
    public static function isFacebookSitesSession()
    {
        $session = System::getContainer()->get('session');
        return ! empty($session->get('tl_facebook_sites'));
    }

    public static function addRejectedPostMessage($date, $model, $message)
    {
        $session = System::getContainer()->get('session');
        $sites = $session->get('tl_facebook_sites');
        $sites['rejectedPosts'][] = $date->format(
            $GLOBALS['TL_CONFIG']['datimFormat']) . ' ' .
             ((! empty($model->title)) ? $model->title : $model->headline) . ' - Ursache: ' .
             $message;
        $session->set('tl_facebook_sites', $sites);
    }

    public static function addErrorMessage($exception)
    {
        $session = System::getContainer()->get('session');
        $sites = $session->get('tl_facebook_sites');
        $sites['errorMessages'][] = $exception->getCode() . ' ' .
             $exception->getMessage();
        $session->set('tl_facebook_sites', $sites);
    }

    public static function addErrorMessageText($message)
    {
        $session = System::getContainer()->get('session');
        $sites = $session->get('tl_facebook_sites');
        $sites['errorMessages'][] = $message;
        $session->set('tl_facebook_sites', $sites);
    }

    public static function updateSessionValuesForResponse($countName, $textName, $title, $date,
        $wasUpdated)
    {
        $session = System::getContainer()->get('session');
        $sites = $session->get('tl_facebook_sites');
        $sites[$countName] ++;

        $text = $date->format($GLOBALS['TL_CONFIG']['datimFormat']) . ' ' . $title;

        \System::loadLanguageFile('tl_facebook_messages');

        if ($wasUpdated) {
            $sites[$textName][] = $text . '<em>' .
                 $GLOBALS['TL_LANG']['tl_facebook_messages']['updated'] . '</em>';
        } else {
            $sites[$textName][] = $text . '<em>' .
                 $GLOBALS['TL_LANG']['tl_facebook_messages']['created'] . '</em>';
        }

        $session->set('tl_facebook_sites', $sites);
    }

    public static function autolink($str, $attributes=array())
    {
        $attrs = '';
        foreach ($attributes as $attribute => $value) {
            $attrs .= " {$attribute}=\"{$value}\"";
        }
        $str = ' ' . $str;
        $str = preg_replace(
          '`((https?|ftps?):\/\/[^"<\s]+)(?![^<>]*>|[^"]*?<\/a)`i',
          '<a href="$1"'.$attrs.'>$1</a>',
        $str
        );
        $str = substr($str, 1);
        $str = preg_replace('`href=\"www`', 'href="http://www', $str);
        // fügt http:// hinzu, wenn nicht vorhanden
        return $str;
    }

    public static function removeHashTag($str)
    {
        return preg_replace('/(^|\s)#(\w+)/', '', $str);
    }

    public static function removeEmoticons($text)
    {
        $clean_text = "";

        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clean_text = preg_replace($regexEmoticons, '', $text);

        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clean_text = preg_replace($regexSymbols, '', $clean_text);

        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clean_text = preg_replace($regexTransport, '', $clean_text);

        // Match Miscellaneous Symbols
        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $clean_text = preg_replace($regexMisc, '', $clean_text);

        // Match Dingbats
        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);

        return $clean_text;
    }

    public static function getToken() {
        $container = \System::getContainer();
        return $container->get('security.csrf.token_manager')->getToken($container->getParameter('contao.csrf_token_name'))->getValue();
    }
}
