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

if (! defined('TL_ROOT')) {
    die('You cannot access this file directly!');
}

$GLOBALS['TL_HOOKS']['executePreActions'][] = array('Postyou\ContaoFacebookConnectorBasicBundle\BackendAjaxHook', 'executePreActions');


if (TL_MODE == 'BE') {
    $GLOBALS['TL_CSS'][] = 'bundles/postyoucontaofacebookconnectorbasic/css/backend.css';
}

$GLOBALS['TL_CSS'][] = 'bundles/postyoucontaofacebookconnectorbasic/css/default.css|static';

$timeStrLookup = array(
                    'monthly' => 'Monthly',
                    'weekly' => 'Weekly',
                    'daily' => 'Daily',
                    'hourly' => 'Hourly',
                    'minutely' => 'Minutely'
                );

foreach ($timeStrLookup as $key => $value) {
    $GLOBALS['TL_CRON'][$key][] = ['postyou_contao_facebook.listener.load_posts', 'set'.$value.'CronJobs'];
}


// BE Module
$GLOBALS['BE_MOD']['Facebook']['Facebook-Seiten'] = array(
    'tables' => array(
        'tl_facebook_sites',
        'tl_facebook_posts',
        'tl_facebook_events',
        'tl_facebook_galleries'
    ),
    'icon' => 'bundles/postyoucontaofacebookconnectorbasic/img/page.png'
);

$GLOBALS['BE_MOD']['Facebook']['Einstellungen'] = array(
    'tables' => array(
        'tl_facebook_settings'
    ),
    'icon' => 'bundles/postyoucontaofacebookconnectorbasic/img/world_edit.png'
);

$GLOBALS['TL_CTE']['Facebook']['facebook_post_list'] = 'Postyou\ContaoFacebookConnectorBasicBundle\FacebookPostList';

$GLOBALS['TL_CONFIG']['facebookApiVersion'] = 'v2.12';

/*
 * Models
 */
$GLOBALS['TL_MODELS']['tl_facebook_sites'] = \Postyou\ContaoFacebookConnectorBasicBundle\FacebookSitesModel::class;
$GLOBALS['TL_MODELS']['tl_facebook_posts'] = \Postyou\ContaoFacebookConnectorBasicBundle\FacebookPostsModel::class;