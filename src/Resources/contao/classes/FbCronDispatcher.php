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


class FbCronDispatcher
{
    private function getPosts($siteID)
    {
        $fbConnector = FbConnector::getInstance(
                        array(
                            'connectionType' => ConnectionType::POST_GET
                        ));
        $fbConnector->getPostsFromSiteIdAndSaveInDb($siteID);
    }

    private function getGaleries($siteID)
    {
        $fbConnector = FbConnector::getInstance(
            array(
                'connectionType' => ConnectionType::GALLERY_GET
            ));
        $fbConnector->getGalleryDataFromSiteId($siteID);
    }

    public function setMinutelyCronJobs()
    {
        $facebookSitesModels = \Contao\Database::getInstance()->execute('SELECT * FROM tl_facebook_sites')->fetchAllAssoc();

        if ($facebookSitesModels) {
            foreach ($facebookSitesModels as $facebookSitesModel) {
                if ($facebookSitesModel['synchronizePosts'] == 1 && $facebookSitesModel['autoSyncPosts'] == 1) {
                    if ($facebookSitesModel['autoSyncPostOptions'] == 5) {
                        $this->getPosts($facebookSitesModel['id']);
                    }
                }
                if ($facebookSitesModel['synchronizeGalleries'] && $facebookSitesModel['autoSyncGalleries'] == 1) {
                    if ($facebookSitesModel['autoSyncGalleryOptions'] == 5) {
                        $this->getGaleries($facebookSitesModel['id']);
                    }
                }
            }
        }
    }

    public function setHourlyCronJobs()
    {
        $facebookSitesModels = \Contao\Database::getInstance()->execute('SELECT * FROM tl_facebook_sites')->fetchAllAssoc();

        if ($facebookSitesModels) {
            foreach ($facebookSitesModels as $facebookSitesModel) {
                if ($facebookSitesModel['synchronizePosts'] == 1 && $facebookSitesModel['autoSyncPosts'] == 1) {
                    if ($facebookSitesModel['autoSyncPostOptions'] == 4) {
                        $this->getPosts($facebookSitesModel['id']);
                    }
                }
                if ($facebookSitesModel['synchronizeGalleries'] && $facebookSitesModel['autoSyncGalleries'] == 1) {
                    if ($facebookSitesModel['autoSyncGalleryOptions'] == 4) {
                        $this->getGaleries($facebookSitesModel['id']);
                    }
                }
            }
        }
    }

    public function setDailyCronJobs()
    {
        $facebookSitesModels = \Contao\Database::getInstance()->execute('SELECT * FROM tl_facebook_sites')->fetchAllAssoc();

        if ($facebookSitesModels) {
            foreach ($facebookSitesModels as $facebookSitesModel) {
                if ($facebookSitesModel['synchronizePosts'] == 1 && $facebookSitesModel['autoSyncPosts'] == 1) {
                    if ($facebookSitesModel['autoSyncPostOptions'] == 3) {
                        $this->getPosts($facebookSitesModel['id']);
                    }
                }
                if ($facebookSitesModel['synchronizeGalleries'] && $facebookSitesModel['autoSyncGalleries'] == 1) {
                    if ($facebookSitesModel['autoSyncGalleryOptions'] == 3) {
                        $this->getGaleries($facebookSitesModel['id']);
                    }
                }
            }
        }
    }

    public function setWeeklyCronJobs()
    {
        $facebookSitesModels = \Contao\Database::getInstance()->execute('SELECT * FROM tl_facebook_sites')->fetchAllAssoc();

        if ($facebookSitesModels) {
            foreach ($facebookSitesModels as $facebookSitesModel) {
                if ($facebookSitesModel['synchronizePosts'] == 1 && $facebookSitesModel['autoSyncPosts'] == 1) {
                    if ($facebookSitesModel['autoSyncPostOptions'] == 2) {
                        $this->getPosts($facebookSitesModel['id']);
                    }
                }
                if ($facebookSitesModel['synchronizeGalleries'] && $facebookSitesModel['autoSyncGalleries'] == 1) {
                    if ($facebookSitesModel['autoSyncGalleryOptions'] == 2) {
                        $this->getGaleries($facebookSitesModel['id']);
                    }
                }
            }
        }
    }

    public function setMonthlyCronJobs()
    {
        $facebookSitesModels = \Contao\Database::getInstance()->execute('SELECT * FROM tl_facebook_sites')->fetchAllAssoc();

        if ($facebookSitesModels) {
            foreach ($facebookSitesModels as $facebookSitesModel) {
                if ($facebookSitesModel['synchronizePosts'] == 1 && $facebookSitesModel['autoSyncPosts'] == 1) {
                    if ($facebookSitesModel['autoSyncPostOptions'] == 1) {
                        $this->getPosts($facebookSitesModel['id']);
                    }
                }
                if ($facebookSitesModel['synchronizeGalleries'] && $facebookSitesModel['autoSyncGalleries'] == 1) {
                    if ($facebookSitesModel['autoSyncGalleryOptions'] == 1) {
                        $this->getGaleries($facebookSitesModel['id']);
                    }
                }
            }
        }
    }
}
