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

use Contao\DataContainer;
use Contao\Dbafs;
use postyou\FacebookPostsDeleteListModel;

$GLOBALS['TL_DCA']['tl_facebook_posts'] = array(
    'config' => array(
        'dataContainer' => 'Table',
        'ptable' => 'tl_facebook_sites',
        'switchToEdit' => true,
        'sql' => array(
            'keys' => array(
                'id' => 'primary',
                'pid' => 'index'
            )
        ),
        'ondelete_callback' => array(
            array(
                'tl_facebook_posts_basic',
                'onDelete'
            )
        ),
        'onsubmit_callback' => array(
            array(
                'tl_facebook_posts_basic',
                'onSubmit'
            )
        ),
        'onload_callback' => array(
            array(
                'tl_facebook_posts_basic',
                'loadLanguage'
            )
        )
    ),
    'list' => array(
        'sorting' => array(
            'mode' => 4,
            'fields' => array(
                "created_time DESC, title, facebookPostType"
            ),
            'panelLayout' => ('filter;search,sort;limit'),
            'disableGrouping' => true,
            'headerFields' => array(
                'title'
            ),
            'child_record_callback' => array(
                'tl_facebook_posts_basic',
                'showInList'
            )
        ),
        'label' => array(
            'fields' => array(
                'title, facebookPostType'
            ),
            'format' => '%s',
            'showColumns' => true,
            'label_callback' => array(
                'tl_facebook_posts_basic',
                'labelCallback'
            )
        ),
        'global_operations' => array(
            'all' => array(
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array( // operationen der einträge
            'edit' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_facebook_posts']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif'
            ),
            'copy' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_facebook_posts']['copy'],
                'href' => 'act=paste&amp;mode=copy',
                'icon' => 'copy.gif',
                'attributes' => 'onclick="Backend.getScrollOffset()"'
            ),
            'delete' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_facebook_posts']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' .
                     $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] .
                     '\'))return false;Backend.getScrollOffset()"'
            ),
            'toggle' => array(
                'label'               => &$GLOBALS['TL_LANG']['tl_facebook_posts']['toggle'],
                'icon'                => 'visible.gif',
                'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback'     => array('tl_facebook_posts_basic', 'toggleIcon')
            ),
            'show' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_facebook_posts']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif',
                'attributes' => 'style="margin-right:3px"'
            )
        )
    ),
    'palettes' => array(
        '__selector__' => array(
            'facebookPostType'
        ),
        'default' => '{title_legend},title, postMessage, facebookPostType;',
        'photo' => '{title_legend},title, postMessage, facebookPostType; {attachment_legend}, multiSRC, floating',
        'link' => '{title_legend},title, postMessage, facebookPostType; {attachment_legend}, multiSRC',
        'video' => '{title_legend},title, postMessage, facebookPostType; {video_legend}, videoLink'
    ),
    'subpalettes' => array(),
    'fields' => array(
        'id' => array(
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid' => array(
            'relation' => array(
                'table' => "tl_facebook_sites",
                "field" => "id",
                'facebookPostType' => 'belongsTo',
                'load' => 'lazy'
            ),
            'sql' => "int(10) unsigned NOT NULL"
        ),
        'tstamp' => array(
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'updated_time' => array(
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'flag' => 12
        ),
        'created_time' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_posts']['created_time'],
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'sorting' => true,
            'flag' => 12
        ),
        'title' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_posts']['title'],
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => array(
                'maxlength' => 255
            ),
            'sql' => "varchar(255) NOT NULL default ''"
        ),
        'facebookPostType' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_posts']['facebookPostType'],
            'inputType' => 'select',
            'search' => 'true',
            'filter' => true,
            'options_callback' => array(
                'tl_facebook_posts_basic',
                'getTypes'
            ),
            'eval' => array(
                'submitOnChange' => true
            ),
            'sql' => "varchar(255) NOT NULL default ''"
        ),
        'icon' => array(
            'exclude' => true,
            'sql' => "varchar(255) NOT NULL default ''"
        ),
        'postId' => array(
            'exclude' => true,
            'sql' => "varchar(255) NOT NULL default ''"
        ),
        'postMessage' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_posts']['postMessage'],
            'inputType' => 'textarea',
            'eval' => array(),
            'sql' => "text NULL"
        ),
        'thumbnailPath' => array(
            'sql' => "text NULL"
        ),
        'videoLink' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_posts']['videoLink'],
            'inputType' => 'text',
            'sql' => "text NULL"
        ),
        'videoId' => array(
            'exclude' => true,
            'sql' => "varchar(256) NULL"
        ),
        'videoClass' => array(
            'exclude' => true,
            'sql' => "varchar(256) NULL"
        ),
        'videoThumb' => array(
            'exclude' => true,
            'sql' => "varchar(256) NULL"
        ),
        'facebookLink' => array(
            'sql' => "text NULL"
        ),
        'multiSRC' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_posts']['multiSRC'],
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => array(
                'multiple' => true,
                'fieldType' => 'checkbox',
                'orderField' => 'orderSRC',
                'files' => true,
                'extensions' => \Config::get('validImageTypes'),
                'isGallery' => true
            ),
            'sql' => "blob NULL"
        ),
        'floating' => array(
                'label'                   => &$GLOBALS['TL_LANG']['tl_content']['floating'],
                'default'                 => 'above',
                'exclude'                 => true,
                'inputType'               => 'radioTable',
                'options'                 => array('above', 'left', 'right', 'below'),
                'eval'                    => array('cols'=>4, 'tl_class'=>'w50'),
                'reference'               => &$GLOBALS['TL_LANG']['MSC'],
                'sql'                     => "varchar(32) NOT NULL default ''"
            ),
        'orderSRC' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['orderSRC'],
            'sql' => "blob NULL"
        ),
        'imageSrcFacebook' => array(
            'sql' => "blob NULL"
        ),
        'published' => array(
            'exclude'                 => true,
            'sql'                     => "char(1) NOT NULL default '1'"
        ),
    )
);

class tl_facebook_posts_basic extends \Backend
{
    public function loadLanguage()
    {
        \System::loadLanguageFile('tl_content');
    }

    public function onSubmit($dc)
    {
        if (empty($dc->activeRecord->created_time)) {
            $this->Database->prepare("UPDATE tl_facebook_posts SET created_time=? WHERE id=?")->execute(
                time(), $dc->id);
        }

        $this->Database->prepare("UPDATE tl_facebook_posts SET updated_time=? WHERE id=?")->execute(
            time(), $dc->id);

        if (empty($dc->activeRecord->title)) {
            $facebookPostsModel = \FacebookPostsModel::findById($dc->activeRecord->id);
            $facebookPostsModel->title = StringUtil::substr($dc->activeRecord->postMessage, 70,
                '...');
            $facebookPostsModel->save();
        }
    }

    public function onLoad($dc)
    {
        return;
    }

    public function onDelete($dc)
    {
        $facebookPostModel = \FacebookPostsModel::findById($dc->activeRecord->id);

        $path = \Config::get('uploadPath') . '/facebook/posts/' . $facebookPostModel->postId;

        $folder = new \Folder($path);
        $folder->purge();
        $folder->delete();

        return $facebookPostModel;
    }

    public function onVersion($dc)
    {
        return;
    }

    public function labelCallback($row, $label, $dc, $args)
    {
        return $args;
    }

    public function showInList($arrRow)
    {
        $date = new DateTime();
        $date->setTimestamp($arrRow['created_time']);
        $icon = '';
        $thumbnail = '';
        if (! empty($arrRow['icon'])) {
            $icon = '<img src="' . $arrRow['icon'] . '" />';
        }

        if (! empty($arrRow['thumbnailPath'])) {
            $thumbnailStr = '<br><img class="thumbnail" width="130" src="' . $arrRow['thumbnailPath'] .
                 '"/>';

            if (strpos($arrRow['thumbnailPath'], 'http') !== false) {
                $thumbnail = $thumbnailStr;
            } else {
                // Check ob Bild korrekt heruntergeladen
                $filesize = filesize(TL_ROOT . '/' . $arrRow['thumbnailPath']);
                if ($filesize && $filesize > 0) {
                    $thumbnail = $thumbnailStr;
                }
            }
        }

        return $icon . ' <em>' . $date->format($GLOBALS['TL_CONFIG']['datimFormat']) . '</em> ' .
             $arrRow['title'] . $thumbnail;
    }

/**
     * Return the "toggle visibility" button
     *
     * @param array  $row
     * @param string $href
     * @param string $label
     * @param string $title
     * @param string $icon
     * @param string $attributes
     *
     * @return string
     */
    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (strlen(Input::get('tid'))) {
            $this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
            $this->redirect($this->getReferer());
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

        if (!$row['published']) {
            $icon = 'invisible.gif';
        }


        return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label, 'data-state="' . ($row['published'] ? 1 : 0) . '"').'</a> ';
    }


    /**
     * Disable/enable a user group
     *
     * @param integer       $intId
     * @param boolean       $blnVisible
     * @param DataContainer $dc
     */
    public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
    {
        // Check permissions to edit
        Input::setGet('id', $intId);
        Input::setGet('act', 'toggle');

        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA']['tl_article']['fields']['published']['save_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_article']['fields']['published']['save_callback'] as $callback) {
                if (is_array($callback)) {
                    $this->import($callback[0]);
                    $blnVisible = $this->$callback[0]->$callback[1]($blnVisible, ($dc ?: $this));
                } elseif (is_callable($callback)) {
                    $blnVisible = $callback($blnVisible, ($dc ?: $this));
                }
            }
        }

        // Update the database
        $this->Database->prepare("UPDATE tl_facebook_posts SET tstamp=". time() .", published='" . ($blnVisible ? '1' : '') . "' WHERE id=?")
                       ->execute($intId);
    }

    public function getTypes()
    {
        return array(
            'link' => 'Link',
            'status' => 'Status',
            'photo' => 'Foto',
            'video' => 'Video'
        );
        // 'offer' => 'Angebot'
    }
}
