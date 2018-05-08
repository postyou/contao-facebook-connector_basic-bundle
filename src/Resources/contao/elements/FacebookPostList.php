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

use Symfony\Component\Filesystem\Filesystem;

class FacebookPostList extends \ContentElement
{
    protected $strTemplate = 'mod_facebook_posts';

    public function __construct($objModule, $strColumn = 'main')
    {
        $GLOBALS['TL_JAVASCRIPT']['video'] = 'bundles/postyoucontaofacebookconnectorbasic/js/video.js';
        parent::__construct($objModule, $strColumn);
        $this->strTemplate = 'mod_facebook_posts';
    }

    public function generate()
    {
        // Backend Ausgabe
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . utf8_strtoupper("Facebook Post List") . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&table=tl_module&act=edit&id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    protected function compile()
    {
        $limit = null;
        $offset = 0;
        $count = 0;

        $siteIds = unserialize($this->facebookSites);
        if (!empty($siteIds)) {


            $postModels = FacebookPostsModel::findBy(
                array(
                    'pid IN (' . implode(',', $siteIds) . ') AND published = "1"'
                ), null, array('order' => 'created_time DESC', 'limit' => $this->maxPosts));

            if ($this->customTpl != '' && TL_MODE == 'FE') {
                $objTemplate = new \FrontendTemplate($this->customTpl);
            } else {
                $objTemplate = new \FrontendTemplate('ce_facebook_posts');
            }

            $total = count($postModels);

            // Split the results
            if ($this->perPage > 0) {

                // Get the current page
                $id = 'page_n' . $this->id;
                $page = (\Input::get($id) !== null) ? \Input::get($id) : 1;

                // Do not index or cache the page if the page number is outside the range
                if ($page < 1 || $page > max(ceil($total / $this->perPage), 1)) {
                    /** @var \PageModel $objPage */
                    global $objPage;

                    /** @var \PageError404 $objHandler */
                    $objHandler = new $GLOBALS['TL_PTY']['error_404']();
                    $objHandler->generate($objPage->id);
                }

                // Set limit and offset
                $limit = $this->perPage;
                $offset += (max($page, 1) - 1) * $this->perPage;

                // Overall limit
                if ($offset + $limit > $total) {
                    $limit = $total - $offset;
                }

                // Adjust the overall limit
                if (isset($limit)) {
                    $total = min($limit, $total);
                }

                // Add the pagination menu
                $objPagination = new \Pagination($total, $this->perPage,
                    \Config::get('maxPaginationLinks'), $id);
                $this->Template->pagination = $objPagination->generate("\n  ");
            }

            $posts = array();

            $postModels = FacebookPostsModel::findBy(
                array(
                    'pid IN (' . implode(',', $siteIds) . ') AND published = "1"'
                ), null,
                array(
                    'offset' => $offset,
                    'limit' => isset($limit) ? $limit : $this->maxPosts,
                    'order' => 'created_time DESC'
                ));

            $pid = null;
        }
        if (!empty($postModels)) {
            while ($postModels->next()) {
                if (! isset($pid) || $pid !== $postModels->current()->pid) {
                    $pid = $postModels->current()->pid;
                    $facebookSiteModel = FacebookSitesModel::findById($pid);
                }

                $objTemplate->title = $postModels->current()->title !== '-' ? $postModels->current()->title : '';

                $objTemplate->facebookLinkHref = $postModels->current()->facebookLink;

                //Textlaenge kuerzen
                if (!empty($this->messageLength) && (strlen($postModels->current()->postMessage) > $this->messageLength)) {
                    $objTemplate->message = \StringUtil::substr($postModels->current()->postMessage, $this->messageLength,
                ' ...');
                    \System::loadLanguageFile('tl_facebook_posts');

                    $objTemplate->facebookLink = '<a target="_blank" href="'.$postModels->current()->facebookLink.'">'.($postModels->current()->type == 'video' ? $GLOBALS['TL_LANG']['tl_facebook_posts']['videoLinkText'] : $GLOBALS['TL_LANG']['tl_facebook_posts']['facebookLinkText']).'</a>';
                } else {
                    $objTemplate->message = $postModels->current()->postMessage;
                    if ($this->showFacebookLinkAlways) {
                        \System::loadLanguageFile('tl_facebook_posts');
                        $objTemplate->facebookLink = '<a target="_blank" href="'.$postModels->current()->facebookLink.'">'.($postModels->current()->type == 'video' ? $GLOBALS['TL_LANG']['tl_facebook_posts']['videoLinkText'] : $GLOBALS['TL_LANG']['tl_facebook_posts']['facebookLinkText']).'</a>';
                    }
                }



                // Link Erkennung
                $objTemplate->message = FbConnectorHelper::autolink($objTemplate->message, array('target' => '_blank'));

                //Hash Tag Entfernen
                if ($postModels->current()->removeHashTag) {
                    $objTemplate->message = FbConnectorHelper::removeHashTag($objTemplate->message);
                }

                // auf null setzen, da Template sonst Wert vom vorhergehenden uebernimmt
                $objTemplate->images = null;
                $objTemplate->imageSrcFacebook = null;

                $images = array();

                if ($facebookSiteModel->saveAttachmentsToFilesystem) {
                    $multiSRC = unserialize($postModels->current()->multiSRC);


                    if (is_array($multiSRC)) {
                        foreach ($multiSRC as $uuid) {
                            $size = unserialize($this->sizeFacebook);
                            $path = \FilesModel::findByUuid($uuid)->path;
                            $tempArray = array();
                            if (!empty($path)) {
                              if (is_array($size) && (!empty($size[0]) || !empty($size[1]) || !empty($size[2]))) {
                                  $pictureFactory =  \System::getContainer()->get('contao.image.picture_factory');
                                  $picture = $pictureFactory->create(TL_ROOT . '/'.\FilesModel::findByUuid($uuid)->path, $size);
                                  $picture = array
                                  (
                                    'img' => $picture->getImg(TL_ROOT, $staticUrl),
                                    'sources' => $picture->getSources(TL_ROOT, $staticUrl)
                                  );
                                  $tempArray['picture'] = $picture;
                              }
                                $tempArray['imagePath'] = $path;
                            }

                            $images[] = $tempArray;
                        }
                        $objTemplate->images = $images;
                    }
                } else {
                    $objTemplate->imageSrcFacebook = $postModels->current()->imageSrcFacebook;
                }

                $objTemplate->floatClass = $postModels->current()->floating;

                $objTemplate->updatedTime = date(\Config::get('datimFormat'),
                    $postModels->current()->updated_time);

                $objTemplate->createdTime = date(\Config::get('datimFormat'),
                    $postModels->current()->created_time);

                $objTemplate->videoLink = $postModels->current()->videoLink;
                $objTemplate->videoId = $postModels->current()->videoId;
                $objTemplate->videoClass = $postModels->current()->videoClass;
                $objTemplate->videoThumb = $postModels->current()->videoThumb;
                $objTemplate->size = unserialize($this->size);


                $cssID = 'facebook-post-' . $count;

                $objTemplate->cssID = $cssID;

                $objTemplate->class = 'facebook-post block ' . ((++ $count == 1) ? ' first' : '') .
                     (($count == $total) ? ' last' : '') . ((($count % 2) == 0) ? ' odd' : ' even');

                $imagePathCount = count($images);
                $objTemplate->beforeStyle = null;
                if ($imagePathCount > 1) {
                    $objTemplate->beforeStyle = '<style>#'.$cssID.' .image_container a.cboxElement:after {
                                    content: "+'.$imagePathCount.'";
                                }</style>';
                }
                $posts[] = $objTemplate->parse();
            }
        }
        $this->Template->posts = $posts;
        $this->Template->empty = $GLOBALS['TL_LANG']['MSC']['emptyList'];
    }
}
