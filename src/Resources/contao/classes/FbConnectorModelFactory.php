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

/**
* Factory Class provides methods to create models for 'Facebookpost Data'
**/
class FbConnectorModelFactory
{

    /**
    * Creates a model to save the 'Facebookpost Data'
    **/
    public static function create($facebookSiteModel, $postId = null)
    {
        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['createFacebookModel']) && is_array($GLOBALS['TL_HOOKS']['createFacebookModel'])) {
            foreach ($GLOBALS['TL_HOOKS']['createFacebookModel'] as $callback) {
                if (($collection = \System::importStatic($callback[0])->{$callback[1]}($facebookSiteModel, $postId)) === false) {
                    continue;
                }

                if ($collection !== null || $collection instanceof \Model\Collection) {
                    return $collection;
                }
            }
        }

        $model = FacebookPostsModel::findByPostId($postId);

        if (empty($model)) {
            $model = new FacebookPostsModel();
            $model->pid = $facebookSiteModel->id;
            $model->postId = $postId;
        }

        //findByPostId liefert Collection zurueck
        if ($model instanceof \Model\Collection) {
            $model = $model->current();
        }

        return new \Model\Collection(array($model), 'tl_facebook_posts');
    }
}
