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

 use Contao\Environment;
 use Facebook\Facebook;
 use postyou\ConnectionType;

$GLOBALS['TL_DCA']['tl_facebook_sites'] = array(
    'config' => array(
        'dataContainer' => 'Table',
        'switchToEdit' => true,
        'ctable' => array(
            'tl_facebook_posts'
        ),
        'sql' => array(
            'keys' => array(
                'id' => 'primary'
            )
        ),

        'onload_callback' => array(
            array(
                'tl_facebook_sites_basic',
                'setPalettes'
            ),
            array(
                'tl_facebook_sites_basic',
                'loadLanguage'
            )
        )
    ),
    'list' => array(
        'sorting' => array(
            'mode' => 2,
            'fields' => array(
                "title"
            ),
            'flag' => 6,
            'panelLayout' => ('search,sort,filter;limit')
        ),

        'label' => array(
            'fields' => array(
                'title'
            ),
            'format' => '%s',
            'showColumns' => true
        ),


        'operations' => array(
            'editPosts' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['editPosts'],
                'href' => 'table=tl_facebook_posts',
                'icon' => 'bundles/postyoucontaofacebookconnectorbasic/img/post.png',
                'button_callback' => array(
                    'tl_facebook_sites_basic',
                    'disableButton'
                )
            ),
            'editEvents' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['editEvents'],
                'href' => 'table=tl_facebook_events',
                'icon' => 'bundles/postyoucontaofacebookconnectorbasic/img/event.png',
                'button_callback' => array(
                    'tl_facebook_sites_basic',
                    'disableButton'
                )
            ),
            'editGalleries' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['editGalleries'],
                'href' => 'table=tl_facebook_galleries',
                'icon' => 'bundles/postyoucontaofacebookconnectorbasic/img/gallery.png',
                'button_callback' => array(
                    'tl_facebook_sites_basic',
                    'disableButton'
                )
            ),
            'editHeader' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['editHeader'],
                'href' => 'act=edit',
                'icon' => 'header.gif'
            ),
            'copy' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['copy'],
                'href' => 'act=paste&amp;mode=copy',
                'icon' => 'copy.gif',
                'attributes' => 'onclick="Backend.getScrollOffset()"'
            ),
            'delete' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' .
                     $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] .
                     '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif',
                'attributes' => 'style="margin-right:3px"'
            )
        )
    ),
    // "edit"=>array(),
    'palettes' => array(
        '__selector__' => array(
            'synchronizePosts',
            'synchronizeEvents',
            'synchronizeGalleries'
        ),
        'default' => '{title_legend},title, facebookAlias;
                      {post_legend:hide}, synchronizePosts;
                      {events_legend:hide}, synchronizeEvents, onlyProVersionTxt;
                      {gallery_legend:hide}, synchronizeGalleries, onlyProVersionTxt ;'
    ),
    'subpalettes' => array(
        'synchronizePosts' => 'maxPostsCount,  minPostsDate, initDatepicker, addPostsToNewsModule, onlyProVersionTxt, saveAttachmentsToFilesystem, headlineType, floating,
                              autoSyncPosts, autoSyncPostOptions, autoSyncPostDisplayScript, downloadButton, publishPostsButton, wrapperScript',
    ),
    'fields' => array(
        'id' => array(
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp' => array(
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'title' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['title'],
            'filter' => true,
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => array(
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class' => 'w50'
            ),
            'sql' => "varchar(255) NOT NULL default ''"
        ),
        'facebookAlias' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['facebookAlias'],
            'filter' => true,
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => array(
                'mandatory' => true,
                'tl_class' => 'w50',
                'submitOnChange' => true
            ),
            'sql' => "text NULL"
        ),
        'onlyProVersionTxt' => array(
            'input_field_callback' => array('tl_facebook_sites_basic', 'onlyProVersionTxt')
        ),
        'synchronizePosts' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['synchronizePosts'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => array(
                'submitOnChange' => true,
                'class' => 'disabled',
                'disabled' => true
            ),
            'load_callback' => array(
                array(
                    'tl_facebook_sites_basic',
                    'checkSynchronizeAvailability'
                )
            ),
            'sql' => "char(1) NOT NULL default ''"
        ),
        'maxPostsCount' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['maxPostsCount'],
            'exclude' => true,
            'inputType' => 'text',
            'eval' => array(
                'rgxp' => 'digit',
                'nospace' => true,
                'maxlength' => 3,
                'tl_class' => 'w50',
                'submitOnChange' => true
            ),
            'sql' => "varchar(64) NOT NULL default ''"
        ),
        'initDatepicker' => array(
            'input_field_callback' => array(
                'tl_facebook_sites_basic',
                'initDatepicker'
            )
        ),
        'minPostsDate' => array(
            'exclude' => true,
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['minPostsDate'],
            'inputType' => 'text',
            'eval' => array(
                'rgxp' => 'date',
                'tl_class' => 'wizard w50',
                'submitOnChange' => true
            ),
            'sql' => "varchar(10) NOT NULL default ''"
        ),
        'addPostsToNewsModule' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['addPostsToNewsModule'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => array(
                'tl_class' => 'clr ctrl_addPostsToNewsModule',
                'submitOnChange' => true,
                'disabled' => true
            ),
            'sql' => "char(1) NOT NULL default ''"
        ),
        'downloadButton' => array(
            'input_field_callback' => array(
                'tl_facebook_sites_basic',
                'loadPostsButton'
            ),
            'eval' => array(
                'doNotShow' => true
            )
        ),
        'publishPostsButton' => array(
                'input_field_callback' => array(
                        'tl_facebook_sites_basic',
                        'publishPostsButton'
                ),
                'eval' => array(
                        'doNotShow' => true
                )
        ),
        'autoSyncPosts' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['autoSync'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => array(
                'tl_class' => 'autoSyncPosts clr',
                'submitOnChange' => true
            ),
            'sql' => "char(1) NOT NULL default ''"
        ),
        'autoSyncPostDisplayScript' => array(
            'input_field_callback' => array(
                'tl_facebook_sites_basic', 'toggleAutoSyncPostOptions'
            ),
            'eval' => array(
                'doNotShow' => true
            )
        ),
        'autoSyncPostOptions' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['autoSyncOptions'],
            'inputType' => 'radio',
            'options_callback' => array('tl_facebook_sites_basic', 'loadAutoSyncOptions'),
            'eval' => array('tl_class' => 'autoSyncPostOptions'),
            // 'save_callback' => array(array('tl_facebook_sites', 'setCron')),
            'sql' => "int(10) unsigned NOT NULL default '1'"
        ),
        'wrapperScript' => array(
            'input_field_callback' => array(
                'tl_facebook_sites_basic',
                'generateWrapper'
            ),
            'eval' => array(
                'doNotShow' => true
            )
        ),
        'reportText' => array(
            'input_field_callback' => array(
                'tl_facebook_sites_basic',
                'generateReportMarkup'
            ),
            'eval' => array(
                'doNotShow' => true
            )
        ),
        'saveAttachmentsToFilesystem' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['saveAttachmentsToFilesystem'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => array(
                'tl_class' => 'clr',
                'submitOnChange' => true
            ),
            'sql' => "char(1) NOT NULL default ''"
        ),
        'loginButton' => array(
            'input_field_callback' => array(
                'tl_facebook_sites_basic',
                'loginButton'
            ),
            'eval' => array(
                'doNotShow' => true
            )
        ),
        'synchronizeEvents' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['synchronizeEvents'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => array(
                'submitOnChange' => true,
                'class' => 'disabled',
                'disabled' => true
            ),
            'load_callback' => array(
                array(
                    'tl_facebook_sites_basic',
                    'checkSynchronizeAvailability'
                )
            ),
            'sql' => "char(1) NOT NULL default ''"
        ),
        'synchronizeGalleries' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['synchronizeGalleries'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => array(
                'submitOnChange' => true,
                'class' => 'disabled',
                'disabled' => true,
            ),
            'load_callback' => array(
                array(
                    'tl_facebook_sites_basic',
                    'checkSynchronizeAvailability'
                )
            ),
            'sql' => "char(1) NOT NULL default ''"
        ),
        'headlineType' => array(
            'label'             => &$GLOBALS['TL_LANG']['tl_facebook_sites']['headlineType'],
            'inputType'         => 'radio',
            'options'           => array('punctuation' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['punctuationCharacter'] ,
                                          'length' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['headlineLength'],
                                        'full_text' => &$GLOBALS['TL_LANG']['tl_facebook_sites']['full_text']),
            'eval'              => array(
                                        'mandatory'   => true,
                                        'tl_class' => 'clr ctrl_headlineType w50',
                                        'submitOnChange' => true
                                    ),
            'default'        => 'length',
            'sql'            => "varchar(255) NOT NULL default 'length'"
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
    )
);

class tl_facebook_sites_basic extends \Backend
{
    public function loadLanguage()
    {
        \System::loadLanguageFile('tl_content');
    }

    public function onlyProVersionTxt()
    {
        return '<div class="tl_info tl_pro_version">'.
                $GLOBALS['TL_LANG']['tl_facebook_sites']['onlyProVersionTxt']
                .'</div>';
    }

    public function onVersion($table, $id, $dc)
    {
        return;
    }

    public function disableButton($row, $href, $label, $title, $icon, $attributes)
    {
        $indicatorStr = '';
        $titleStr = '';

        $disableButton = false;

        if (strpos($attributes, 'editPosts') !== false) {
            if (! $row['synchronizePosts']) {
                $disableButton = true;
                $titleStr = 'disabledSynchronizePostsText';
            } elseif ($row['addPostsToNewsModule']) {
                $disableButton = true;
                $titleStr = 'disabledNewsModuleText';
            }
        } elseif (strpos($attributes, 'editEvents') !== false) {
            if (! $row['synchronizeEvents']) {
                $disableButton = true;
                $titleStr = 'disabledSynchronizeEventsText';
            } elseif ($row['addEventsToCalendarModule']) {
                $disableButton = true;
                $titleStr = 'disabledEventsModuleText';
            }
        } elseif (strpos($attributes, 'editGalleries') !== false) {
            if (! $row['synchronizeGalleries']) {
                $disableButton = true;
                $titleStr = 'disabledSynchronizeGalleriesText';
            }
        }

        return (! $disableButton) ? '<a href="' .
             $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) .
             '"' . $attributes . '>' . Image::getHtml($icon, $label) . '</a> ' : '<a title="' .
             $GLOBALS['TL_LANG']['tl_facebook_sites'][$titleStr] . '">' . Image::getHtml(
                preg_replace('/\.png$/i', '_.png', $icon)) . '</a> ';
    }

    public function labelCallback($row, $label, $dc, $args)
    {
        return $args;
    }

    public function editButtonPrint()
    {
        return;
    }

    public function checkSynchronizeAvailability($varValue, $dc)
    {
        if ($dc->field == 'synchronizePosts') {
            if (! empty($dc->activeRecord->facebookAlias)) {
                $GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['eval']['class'] = '';
                $GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['eval']['disabled'] = false;
            }
        }

        return $varValue;
    }

    public function initDatepicker()
    {
//        $format = \Date::formatToJs(\Config::get('dateFormat'));
//
//        return \Image::getHtml('assets/mootools/datepicker/' . $GLOBALS['TL_ASSETS']['DATEPICKER'] . '/icon.gif', '', 'title="'.specialchars($GLOBALS['TL_LANG']['MSC']['datepicker']).'" id="toggle_minPostsDate" style="vertical-align:-4px; margin-left: 4px; cursor:pointer"').'
//
//        <script>
//            var target = document.getElementById("ctrl_minPostsDate");
//            var parent = target.parentNode;
//            var newElement = document.getElementById("toggle_minPostsDate");
//
//            parent.insertBefore(newElement, target.nextSibling);
//            window.addEvent("domready", function() {
//
//              new Picker.Date($("ctrl_minPostsDate"), {
//                draggable: false,
//                toggle: $("toggle_minPostsDate"),
//                format: "' . $format . '",
//                positionOffset: {x:-211,y:-209},
//                pickerClass: "datepicker_bootstrap",
//                useFadeInOut: !Browser.ie,
//                startDay: ' . $GLOBALS['TL_LANG']['MSC']['weekOffset'] . ',
//                titleFormat: "' . $GLOBALS['TL_LANG']['MSC']['titleFormat'] . '",
//                onSelect: function(){document.getElementById("ctrl_minPostsDate").onchange();}
//              });
//            });
//
//        </script>';
    }

    public function generateWrapper()
    {
        return '<script>
    		var downloadWrapper = document.getElementsByClassName("downloadWrapper")[0];
    		var autoSyncPosts = document.getElementsByClassName("autoSyncPosts")[0];
    		var autoSyncPostOptions = document.getElementsByClassName("autoSyncPostOptions")[0];

    		downloadWrapper.appendChild(autoSyncPosts);
    		downloadWrapper.appendChild(autoSyncPostOptions);

    	</script>';
    }

    public function toggleAutoSyncPostOptions()
    {
        return '<script>

                    var autoSyncPostCheckbox = document.getElementById("opt_autoSyncPosts_0");

                    if (autoSyncPostCheckbox.addEventListener) {
                        autoSyncPostCheckbox.addEventListener("click", toggleAutoSyncPostOptions);
                        window.addEventListener("load", toggleAutoSyncPostOptions);
                    } else if (autoSyncPostCheckbox.attachEvent) {
                        autoSyncPostCheckbox.attachEvent("onclick", toggleAutoSyncPostOptions);
                        window.attachEvent("onload", toggleAutoSyncPostOptions);
                    }

                    function toggleAutoSyncPostOptions() {
                        var autoSyncPostsEnabled = document.getElementById("opt_autoSyncPosts_0").checked;
                        if (autoSyncPostsEnabled) {
                            document.getElementsByClassName("autoSyncPostOptions")[0].style.display = "block";
                        } else {
                            document.getElementsByClassName("autoSyncPostOptions")[0].style.display = "none";
                        }
                    }


            </script>';
    }


    /**
     * Button functions *
     */
     public function loadPostsButton()
     {
         return '<div class="downloadWrapper w50 clr"><div id="downloadButton" class="downloadButton"><a type="button" onclick="getLoadPostsLink(); return false;" class="tl_submit">' .
              $GLOBALS['TL_LANG']['tl_facebook_sites']['downloadButton'] . '</a></div></div>'.$this->getButtonScript('getLoadPostsLink','loadPosts');
     }

    public function publishPostsButton()
    {
        return '<div class="publishWrapper w50"><div id="publishPostsButton" class="publishButton"><a type="button"
                 class="tl_submit disabled">'
                . $GLOBALS['TL_LANG']['tl_facebook_sites']['publishPostsButton'] .
                '</a></div>'.$this->onlyProVersionTxt().'</div>';
    }





    protected function loginButton($showExplanation = true)
    {
        return ($showExplanation ? '<div class="tl_help loginExplanation">'.
             $GLOBALS['TL_LANG']['tl_facebook_sites']['loginExplanation'].'</div>' : '') .
             '<div><a id="facebookLogin" class="tl_submit disabled">' . $GLOBALS['TL_LANG']['tl_facebook_sites']['facebookLoginLink'] . '</a></div>';
    }

    /**
     * redirect generate functions *
     */
    public function generateReportMarkup()
    {
        $session = System::getContainer()->get('session');

        if (empty($session->get('tl_facebook_sites'))) {
            return;
        }

        $countText = '';
        $contentText = '';
        $deletedCountText = '';
        $rejectedText = '';
        $subject = '';
        $verb = 'synchronisiert';

        if (\Input::get('reportTypePost')) {
            $subject = 'Post';
            $deletedCountText = 'deletedPostsCount';
            $rejectedText = 'rejectedPosts';
            if (Input::get('directionGet')) {
                $countText = 'savedPostsCount';
                $contentText = 'savedPosts';
            } elseif (Input::get('directionPublish')) {
                $countText = 'publishedPostsCount';
                $contentText = 'publishedPosts';
            }
        } elseif (\Input::get('reportTypeEvent')) {
            $subject = 'Event';
            $deletedCountText = 'deletedEventsCount';
            $rejectedText = 'rejectedEvents';
            $countText = 'savedEventsCount';
            $contentText = 'savedEvents';
        } elseif (\Input::get('reportTypeGallery')) {
            $subject = 'Album';
            $countText = 'savedGalleriesCount';
            $contentText = 'savedGalleries';
        }

        if (\Input::get('directionPublish')) {
            $verb = 'ver&ouml;ffentlicht';
        }

        $html = '<div class="clr widget" id="synchronizeReport"><h2>Synchronisations &Uuml;berblick:</h2>';

        if ($session->get('tl_facebook_sites')[$countText] === 0 ||
             empty($session->get('tl_facebook_sites')[$countText])) {
            if ($subject === 'Album') {
                $html .= '<p class="neutral">Es wurden keine Alben ' . $verb . ', da keine neuen oder aktualisierten Inhalte gefunden wurden.</p>';
            } else {
                $html .= '<p class="neutral">Es wurden keine ' . $subject . 's ' . $verb . ', da keine neuen oder aktualisierten Inhalte gefunden wurden.</p>';
            }
        } else {
            if ($session->get('tl_facebook_sites')[$countText] == 1) {
                $html .= '<p class="success">Es wurde ' . $session->get('tl_facebook_sites')[$countText] .
                     ' ' . $subject . ' ' . $verb . '.</p>';
            } else {
                if ($subject === 'Album') {
                    $html .= '<p class="success">Es wurden ' .
                         $session->get('tl_facebook_sites')[$countText] . ' Alben ' . $verb . '.</p>';
                } else {
                    $html .= '<p class="success">Es wurden ' .
                         $session->get('tl_facebook_sites')[$countText] . ' ' . $subject . 's ' . $verb .
                         '.</p>';
                }
            }
        }

        $html .= '<table id="reportTableSuccess" class="reportTable">';
        if (is_array($session->get('tl_facebook_sites')[$contentText])) {
            foreach ($session->get('tl_facebook_sites')[$contentText] as $row) {
                $html .= '<tr><td class="success">' . $row . '</td></tr>';
            }
        }

        $html .= '</table>';

        if (! empty($session->get('tl_facebook_sites')[$rejectedText])) {
            $html .= '<p class="error">Folgende ' . $subject . 's wurden abgewiesen:</p>';
            $html .= '<table id="reportTableError" class="reportTable">';
            if (is_array($session->get('tl_facebook_sites')[$rejectedText])) {
                foreach ($session->get('tl_facebook_sites')[$rejectedText] as $row) {
                    $html .= '<tr><td class="error">' . $row . '</td></tr>';
                }
            }
            $html .= '</table>';
        }

        if (isset($session->get('tl_facebook_sites')[$deletedCountText])) {
            if ($session->get('tl_facebook_sites')[$deletedCountText] == 1) {
                $html .= '<p class="deleted">Es wurde ' .
                     $session->get('tl_facebook_sites')[$deletedCountText] . ' ' . $subject .
                     ' gel&ouml;scht.</p>';
            } else {
                $html .= '<p class="deleted">Es wurden ' .
                     $session->get('tl_facebook_sites')[$deletedCountText] . ' ' . $subject .
                     ' gel&ouml;scht.</p>';
            }
        }

        $html .= '</div>';

        if (isset($session->get('tl_facebook_sites')['errorMessages'])) {
            foreach ($session->get('tl_facebook_sites')['errorMessages'] as $errorMessage) {
                $html .= '<div class="clr"><h3>Fehlermeldungen:</h3><p class="error">' .
                     $errorMessage . '</p></div>';
            }
        }

        $session->remove('tl_facebook_sites');

        return $html;
    }

    /**
     * onload_callback setPalettes
     */
    public function setPalettes($dc)
    {
        if (\Input::get('reportTypePost')) {
            $GLOBALS['TL_DCA']['tl_facebook_sites']['palettes']['default'] = str_replace(
                'synchronizePosts', 'synchronizePosts, reportText,',
                $GLOBALS['TL_DCA']['tl_facebook_sites']['palettes']['default']);
        }
    }

    public function loadAutoSyncOptions($dc)
    {
        $options = array(
            '1' => $GLOBALS['TL_LANG']['tl_facebook_sites']['autoSyncOptions']['monthly'],
            '2' => $GLOBALS['TL_LANG']['tl_facebook_sites']['autoSyncOptions']['weekly'],
            '3' => $GLOBALS['TL_LANG']['tl_facebook_sites']['autoSyncOptions']['daily'],
            '4' => $GLOBALS['TL_LANG']['tl_facebook_sites']['autoSyncOptions']['hourly'],
            '5' => $GLOBALS['TL_LANG']['tl_facebook_sites']['autoSyncOptions']['minutely']
        );

        return $options;
    }

    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        if (strlen(Input::get('tid'))) {
            $this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1),
                (@func_get_arg(12) ?: null));
            $this->redirect($this->getReferer());
        }

        $href .= '&amp;tid=' . $row['id'] . '&amp;state=' . ($row['published'] ? '' : 1);
        if (! $row['published']) {
            $icon = 'invisible.gif';
        }
        $objPage = $this->Database->prepare("SELECT * FROM tl_new WHERE id=?")
            ->limit(1)
            ->execute($row['id']);

        return '<a href="' . $this->addToUrl($href) . '" title="' . specialchars($title) . '"' .
             $attributes . '>' .
             Image::getHtml($icon, $label, 'data-state="' . ($row['published'] ? 1 : 0) . '"') .
             '</a> ';
    }

    /**
     * Toggle the visibility of an element
     *
     * @param integer $intId
     * @param boolean $blnVisible
     * @param DataContainer $dc
     */
    public function toggleVisibility($intId, $blnVisible, DataContainer $dc = null)
    {
        // Set the ID and action
        Input::setGet('id', $intId);
        Input::setGet('act', 'toggle');
        if ($dc) {
            $dc->id = $intId; // see #8043
        }

        // Update the database
        $this->Database->prepare(
            "UPDATE tl_new SET tstamp=" . time() . ", published='" . ($blnVisible ? 1 : '') .
                 "' WHERE id=?")->execute($intId);
    }

    protected function getButtonScript($methodName, $action) {
        return '<script>
     				function '.$methodName.'() {
     	                AjaxRequest.displayBox("<div class=\'inside\'>" + Contao.lang.loading + " &#46;&#46;&#46;<br><br> '. $GLOBALS['TL_LANG']['tl_facebook_sites']['waitingTimeText'] .'</div>");

                       var url = location.href;
                       if (location.href.indexOf("&code") > -1) {
    	                    url = location.href.substr(0, location.href.indexOf("&code"));
                       }

                       new Request.JSON({
                         url: url,
                         noCache: true,
                         data: "REQUEST_TOKEN=" + Contao.request_token + "&action='.$action.'",
                         onFailure: function(response) {
                             console.log(response);
                           response = JSON.parse(response.response);
                           AjaxRequest.hideBox();
                           Backend.openModalWindow(500, response.title, response.exception);
                         },
                         onSuccess: function(response) {
                             console.log(response);
                           location.href = response;
                         },
                         onError: function(response, error) {
                           response = JSON.parse(response.response);
                           AjaxRequest.hideBox();
                           Backend.openModalWindow(500, response.title, response.exception);
                         }
                       }).send();
     				}

     			</script>';
    }

}
