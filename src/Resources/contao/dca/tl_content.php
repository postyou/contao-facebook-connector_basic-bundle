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

$GLOBALS['TL_DCA']['tl_content']['palettes']['facebook_post_list'] = '{type_legend},type,headline,headlineOptn,facebookSites,perPage,sizeFacebook,maxPosts,messageLength, showFacebookLinkAlways, removeHashTag';

$GLOBALS['TL_DCA']['tl_content']['fields']['facebookSites'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_content']['facebookSites'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array(
        'tl_class' => 'clr',
        'multiple' => true,
        'mandatory' => true
    ),
    'foreignKey' => 'tl_facebook_sites.title',
    'relation' => array(
        'type' => 'hasMany',
        'load' => 'lazy'
    ),
    'sql' => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['sizeFacebook'] = $GLOBALS['TL_DCA']['tl_content']['fields']['size'];
$GLOBALS['TL_DCA']['tl_content']['fields']['sizeFacebook']['label'] =  &$GLOBALS['TL_LANG']['tl_content']['sizeFacebook'];


$GLOBALS['TL_DCA']['tl_content']['fields']['maxPosts'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_content']['maxPosts'],
    'exclude' => true,
    'inputType' => 'text',
    'eval' => array(
        'tl_class' => 'clr',
        'default' => 0,
        'rgxp'=>'natural'
    ),
    'sql' => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['messageLength'] = array(
    'label'             => &$GLOBALS['TL_LANG']['tl_content']['messageLength'],
    'inputType'         => 'text',
    'default'           => '160',
    'eval'              => array(
                                'mandatory'   => true
                            ),
    'sql'            => "varchar(255) NOT NULL default '160'"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['showFacebookLinkAlways'] = array(
    'label'             => &$GLOBALS['TL_LANG']['tl_content']['showFacebookLinkAlways'],
    'inputType'         => 'checkbox',
    'sql'            => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_content']['fields']['removeHashTag'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_content']['removeHashTag'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => array(
        'tl_class' => 'clr'
    ),
    'sql' => "char(1) NOT NULL default ''"
);
