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

$GLOBALS['TL_DCA']['tl_facebook_settings'] = array(
    'config' => array(
        'dataContainer' => 'File'
    ),

    'palettes' => array(
        '__selector__' => array(),
        'default' => 'appID, appSecret, userAccessToken, facebookApiVersion;'
    ),
    'subpalettes' => array(),
    'fields' => array(

        'appID' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_settings']['appID'],
            'inputType' => 'text',
            'eval' => array(
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50'
            )
        ),
        'appSecret' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_settings']['appSecret'],
            'inputType' => 'text',
            'eval' => array(
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50'
            )
        ),
        'facebookApiVersion' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_settings']['facebookApiVersion'],
            'inputType' => 'text',
            'default' => 'v2.12',
            'load_callback' => array(
                function($varValue) {
                    if (empty($varValue)) {
                        return 'v2.12';
                    }
                       return $varValue;
                }
            ),
            'eval' => array(
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50'
            )
        ),
        'userAccessToken' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_settings']['userAccessToken'],
            'inputType' => 'text',
            'eval' => array(
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'clr'
            )
        )
    )
);
