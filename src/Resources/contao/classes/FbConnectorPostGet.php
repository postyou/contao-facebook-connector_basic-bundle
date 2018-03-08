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

use Contao\StringUtil;
use Contao\Dbafs;
use Contao\System;

class FbConnectorPostGet extends FbConnector
{
    private $fields = array(
        'message',
        'updated_time',
        'type',
        'icon',
        'created_time',
        'full_picture',
        'link',
        'source',
        'attachments',
        'permalink_url'
    );

    public function addFields(array $fields)
    {
        $this->fields = array_merge($this->fields, $fields);
        return $this;
    }

    public function getPostsForAlias($facebookAlias)
    {
        $url = parent::getBaseUrl() . '/' . parent::getVersion() . '/' . $facebookAlias . '/posts?' .
           parent::getAccessTokenQuery();


        if (! empty($this->fields)) {
            $url .= '&fields=';
            foreach ($this->fields as $index => $field) {
                if ($index === 0) {
                    $url .= $field;
                } else {
                    $url .= ',' . $field;
                }
            }
        }

        if (! empty($this->limit) && $this->limit < 25) {
            $url .= '&limit=' . $this->limit;
        } else {
            $url .= '&limit=25'; // Default Value
        }

        if (! empty($this->since)) {
            $url .= '&since=' . $this->since;
        }

        $facebookPosts = parent::fetchData($url);

        $facebookPostData = $facebookPosts['data'];
        $facebookPostData = $this->clearPostData($facebookPostData);

        $limitCounter = count($facebookPostData);

        if ($limitCounter <= $this->limit || empty($this->limit)) {
            while (! empty($facebookPosts['paging']['next']) &&
                 ($limitCounter < $this->limit || empty($this->limit))) {
                $facebookPosts = parent::fetchData($facebookPosts['paging']['next']);

                if (empty($this->limit)) {
                    // kein Limit gesetzt alle Daten mergen
                    $facebookPostData = array_merge($facebookPostData, $this->clearPostData($facebookPosts['data']));
                } else {
                    if ((($limitCounter + count($facebookPosts['data'])) <= $this->limit)) {
                        // wenn aktueller Counter + aktuelle Daten das Limit noch nicht erreicht alle Daten mergen
                        $facebookPostData = array_merge($facebookPostData, $this->clearPostData($facebookPosts['data']));
                    } else {
                        // fehlende Daten bis Limit erreicht mergen
                        $diff = $this->limit - $limitCounter;
                        for ($i = 0; $i < $diff; $i ++) {
                            if ($this->isUsefulFacebookPost($facebookPosts['data'][$i])) {
                                $facebookPostData[] = $facebookPosts['data'][$i];
                                $limitCounter ++;
                            } else {
                                $limitCounter --;
                            }
                        }
                    }
                }
            }
        }

        return $facebookPostData;
    }

    public function getPostsFromSiteIdAndSaveInDb($id)
    {
        $facebookSiteModel = FacebookSitesModel::findById($id);
        $this->getPostDataForFacebookSite($facebookSiteModel);
    }

    public function getPostsFromAllSitesAndSaveInDb()
    {
        $faceBookSitesModelCollection = FacebookSitesModel::findAll();
        if (! empty($faceBookSitesModelCollection)) {
            foreach ($faceBookSitesModelCollection as $faceBookSiteModel) {
                $this->getPostDataForFacebookSite($faceBookSiteModel);
            }
        }
    }

    private function getPostDataForFacebookSite($facebookSiteModel)
    {
        if (! empty($facebookSiteModel->maxPostsCount)) {
            $this->setLimit($facebookSiteModel->maxPostsCount);
        }

        if (! empty($facebookSiteModel->minPostsDate)) {
            $this->setSince($facebookSiteModel->minPostsDate);
        }

        $facebookPostData = $this->getPostsForAlias($facebookSiteModel->facebookAlias);

        if (! empty($facebookPostData)) {
            foreach ($facebookPostData as $post) {
                $this->savePostInDb($post, $facebookSiteModel);
            }
        }
        return $facebookPostData;
    }

    private function savePostInDb($post, $facebookSiteModel, $newsArchive = null)
    {
        try {
            $modelCollection = \Postyou\ContaoFacebookConnectorBasicBundle\FbConnectorModelFactory::create($facebookSiteModel, $post['id']);

            foreach ($modelCollection as $model) {
                $postWasUpdated = true;
                $date = new \DateTime($post['updated_time']);

                if ($model->tstamp < $date->getTimestamp()) {
                    $postWasUpdated = false;
                } elseif (!empty($model->created_time) || !empty($model->time)) {
                    // Model existiert bereits und wurde in Contao ueberschrieben
                    return;
                }

                $title;
                $message;
                $searchStr;

                switch ($post['type']) {
                  case 'link':
                  case 'video':
                      $searchStr = $post['message'] ?: $post['attachments']['data'][0]['description'];
                      break;
                  case 'status':
                  case 'photo':
                  case 'event':
                  default:
                      $searchStr = $post['message'];
                      break;
                }

                $searchStr = htmlentities($searchStr,ENT_QUOTES, 'UTF-8');
                $searchStr = \Postyou\ContaoFacebookConnectorBasicBundle\FbConnectorHelper::removeEmoticons($searchStr);

                if ($facebookSiteModel->headlineType == 'length') {
                    $this->splitTitleAndMessage($title, $message, $searchStr);
                } elseif ($facebookSiteModel->headlineType == 'punctuation') {
                    $matches = $this->getHeadlineAndMessage($searchStr);
                    if (strlen($matches[1]) > 110) {
                        $this->splitTitleAndMessage($title, $message, $searchStr);
                    } else {
                        $title = $matches[1];
                        $message = $matches[count($matches)-1];
                    }
                }

                $dateCreated = new \DateTime($post['created_time']);

                $arrData = array(
                    'title' => $title,
                    'message' => $message,
                    'icon' => $post['icon'],
                    'facebookPostType' => $post['type'],
                    'dateCreated' => $dateCreated->getTimestamp(),
                    'dateUpdated' => $date->getTimestamp()
                );

                $model->addSpecificData($arrData);



                if ($post['type'] == 'photo') {
                    $model->facebookLink = $post['attachments']['data'][0]['url'];
                } else {
                    $model->facebookLink = $post['permalink_url'];
                }

                $model->tstamp = $date->getTimestamp();

                if ($post['type'] == 'video') {
                    $key;
                    if (! empty($post['source'])) {
                        $key = 'source';
                    } else {
                        $key = 'link';
                        if (strpos($post[$key], '?')) {
                            $post[$key] = substr($post[$key], 0, strrpos($post[$key], '?'));
                        }
                    }


                    $hasVideoId = true;
                    if (strpos($post[$key], 'youtube')) {
                        $pos = strpos($post['source'], 'embed/') + 6;
                        $model->videoClass = 'youtube';
                    } elseif (strpos($post[$key], 'vimeo')) {
                        $pos = strrpos($post[$key], '/') + 1;
                        $model->videoClass = 'vimeo';
                    } else {
                        $model->videoClass = 'other';
                        $hasVideoId = false;
                    }

                    if ($hasVideoId) {
                        $videoId = substr($post[$key], $pos, strlen($post[$key]) - $pos);


                        if (strpos($videoId, '?') !== false) {
                            $videoId = substr($videoId, 0, strpos($videoId, '?'));
                        }
                        $model->videoId = $videoId;
                    }

                    $model->videoLink = $post[$key];
                    $model->videoThumb = $post['full_picture'];
                }

                $model->source = 'facebook';

                $model = $model->save();

                // Bild von externem Link
                if (! empty($post['full_picture']) && $post['type'] === 'link' && $post['attachments']['data'][0]['type'] !== 'multi_share') {
                    if (strpos($post['full_picture'], '://external')) {
                        $queryArr = $this->getQueryArr($post['full_picture']);
                    }

                    if ($facebookSiteModel->saveAttachmentsToFilesystem) {
                        $pictureModel = $this->savePictureToFilesystem($post['id'],
                        $queryArr['url'] ?: $post['full_picture'], true, $post['id'],
                        $model->getFolderPath());

                        if (isset($pictureModel)) {
                            $model->addSpecificData(array(
                          'addImage' => true,
                          'singleSRC' => $pictureModel->uuid,
                          'fullsize' => true,
                          'floating' => $facebookSiteModel->floating,
                          'multiSRC' => $this->addBlobData($model->multiSRC,
                              $pictureModel->uuid)
                        ));
                        }
                    } else {
                        $model->imageSrcFacebook = $this->addBlobData($model->imageSrcFacebook,
                        $queryArr['url'] ?: $post['full_picture']);
                        $model->addSpecificData(array(
                          'floating' => $facebookSiteModel->floating
                        ));
                    }

                    $model = $model->save();
                }

                if (! empty($post['attachments'])) {
                    if ($post['attachments']['data'][0]['type'] == 'album' ||
                    $post['attachments']['data'][0]['type'] == 'new_album' ||
                     $post['attachments']['data'][0]['type'] == 'photo' ||
                     $post['attachments']['data'][0]['type'] == 'cover_photo'||
                     $post['attachments']['data'][0]['type'] == 'event' ||
                     $post['attachments']['data'][0]['type'] == 'link' ||
                     $post['attachments']['data'][0]['type'] == 'share' ||
                     $post['attachments']['data'][0]['type'] == 'multi_share') {
                        if (!empty($post['attachments']['data'][0]['subattachments']) &&
                        ($post['attachments']['data'][0]['type'] == 'album' ||
                        $post['attachments']['data'][0]['type'] == 'new_album' ||
                        $post['attachments']['data'][0]['type'] == 'share' ||
                        $post['attachments']['data'][0]['type'] == 'multi_share')) {
                            $attachmentData = $post['attachments']['data'][0]['subattachments']['data'];
                        } else {
                            $attachmentData = $post['attachments']['data'];
                        }

                        $contentModelId;
                        foreach ($attachmentData as $index => $subAttachment) {
                            if (($post['attachments']['data'][0]['type'] == 'multi_share' ||
                                $post['attachments']['data'][0]['type'] == 'share') && $index > 0) {
                                break;
                            }

                            $imageSrc = $subAttachment['media']['image']['src'];

                            if (!strpos($imageSrc, '://external')) {
                              if ($facebookSiteModel->saveAttachmentsToFilesystem) {
                                  $fileName;
                                  if ($subAttachment['type'] == 'share') {
                                      $fileName = 'multi_share_'.$index;
                                  } else {
                                      $fileName = $subAttachment['target']['id'];
                                  }

                                  $pictureModel = $this->savePictureToFilesystem($post['id'],
                                  $imageSrc, true,
                                  $fileName,
                                  $model->getFolderPath());

                                  if (isset($pictureModel)) {
                                      $model->multiSRC = $this->addBlobData($model->multiSRC,
                                      $pictureModel->uuid);
                                      $model->addSpecificData(array('orderSRC' => $model->multiSRC,
                                      'multiSRC' => $model->multiSRC));

                                      if ($index === 0) {
                                          $model->addSpecificData(array(
                                                'addImage' => true,
                                                'singleSRC' => $pictureModel->uuid,
                                                'fullsize' => true,
                                                'floating' => $facebookSiteModel->floating,
                                              ));
                                      }
                                  }
                              } else {
                                $model->imageSrcFacebook = $this->addBlobData($model->imageSrcFacebook,
                                $imageSrc);
                                  $model->addSpecificData(array(
                                      'floating' => $facebookSiteModel->floating
                                  ));
                              }
                            }

                            $model->save();
                        }
                    }
                }

                \Postyou\ContaoFacebookConnectorBasicBundle\FbConnectorHelper::updateSessionValuesForResponse('savedPostsCount', 'savedPosts',
                isset($model->title) ? $model->title : $model->headline, $dateCreated,
                $postWasUpdated);
            }
        } catch (\Throwable $e) {
            // echo '500 Internal Server Error';
            // echo $e;
            // exit();
        }
    }

    private function savePictureToFilesystem($postId, $imageSrc, $isAttachment = false,
        $attachmentId = null, $postFolderPath)
    {
        $folder = new \Folder($postFolderPath);


        $fileType = $this->getFileTypeFromUrl($imageSrc);

        if (empty($fileType)) {
          return;
        }

        if ($isAttachment) {
            $picturePath = $postFolderPath . '/attachment_' . $attachmentId . $fileType;
        } else {
            $picturePath = $postFolderPath . '/thumbnail' . $fileType;
        }

        $file = new \File($picturePath);
        if ($file->exists()) {
            $file->delete();
        }

        $ch = curl_init(urldecode($imageSrc));
        $fp = fopen(TL_ROOT . '/' . $picturePath, 'w');
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $st = curl_exec($ch);
        curl_close($ch);

        fclose($fp);

        if ($this->isValidImage(TL_ROOT . '/' . $picturePath)) {
            $pictureModel = Dbafs::addResource($picturePath);
        } else {
            $folder->purge();
            $folder->delete();

            $pictureModel = null;
        }

        return $pictureModel;
    }

    private function clearPostData($facebookPostData)
    {
        $clearedPostData = array();
        foreach ($facebookPostData as $post) {
            // Posts ohne Textinhalt und Bild werden nicht synchronisiert
            // geteilte Events ohne Text werden nicht synchronisiert
            // geaendertes Profilbild oder Titelbild ohne Textmeldung wird nicht synchronisiert
            if (!$this->isUsefulFacebookPost($post)) {
                continue;
            }

            $clearedPostData[] = $post;
        }

        return $clearedPostData;
    }

    private function isUsefulFacebookPost($post)
    {
        if ((empty($post['message']) && empty($post['attachments']))
          || ($post['type'] === 'event' && empty($post['message']))
          || ($post['type'] === 'link' && empty($post['message']) && empty($post['attachments']['data'][0]['description']))
        || ($post['type'] === 'photo' && ($post['attachments']['data'][0]['type'] == 'cover_photo' || ($post['attachments']['data'][0]['type'] == 'profile_media')) && empty($post['message']))) {
            return false;
        }

        return true;
    }

    private function loadAttachmentData($postId)
    {
        $url = parent::getBaseUrl() . '/' . parent::getVersion() . '/' . $postId . '/attachments?' .
             parent::getAccessTokenQuery();
        return parent::fetchData($url);
    }

    private function getFileTypeFromUrl($imageSrc)
    {
        $filteredStr = substr($imageSrc, strrpos($imageSrc, '.') + 1);
        $fileType = '';
        for ($i = 0; $i < strlen($filteredStr); $i++) {
          if ($filteredStr[$i] == '?' || $filteredStr[$i] == '&' || ctype_digit($filteredStr[$i])) {
            break;
          }
          $fileType .= $filteredStr[$i];
        }

        return $fileType;
    }

    private function addBlobData($serializedData, $dataToAdd)
    {
        $data = unserialize($serializedData);
        if (! isset($data) && ! is_array($data)) {
            $data = array();
        }

        $data[] = $dataToAdd;
        return serialize($data);
    }

    private function isValidImage($path)
    {
        list($width, $height, $type, $attr) = getimagesize($path);

        if (isset($type) && in_array($type,
            array(
                IMAGETYPE_PNG,
                IMAGETYPE_JPEG,
                IMAGETYPE_GIF,
                IMAGETYPE_BMP,
                IMAGETYPE_IFF,
                IMAGETYPE_WBMP,
                IMAGETYPE_WEBP
            ))) {
            return true;
        }

        return false;
    }

    private function getHeadlineAndMessage($str)
    {
        $matches = "";
        preg_match('/(.*?[?!]|.*?(?=[.]\s)|.*)([.]*\s*)?([\s\S]*)/', $str, $matches);
        return $matches;
    }

    private function getQueryArr($url)
    {
        $query = parse_url($url, PHP_URL_QUERY);
        $queryArr = array();
        parse_str($query, $queryArr);
        return $queryArr;
    }

    private function splitTitleAndMessage(&$title, &$message, $searchStr)
    {
        $intCharCount = 0;
        $arrChunks = preg_split('/\s+/', $searchStr);
        $arrWordsTitle = array();
        $arrWordsMessage = array();

        foreach ($arrChunks as $strChunk) {
            $intCharCount += utf8_strlen(StringUtil::decodeEntities($strChunk));

            if ($intCharCount++ <= 70) {
                $arrWordsTitle[] = $strChunk;
                continue;
            } else {
                $arrWordsMessage[] = $strChunk;
                continue;
            }
        }

        $title = implode(' ', $arrWordsTitle).' ...';
        $message = '... '.implode(' ', $arrWordsMessage);
    }
}
