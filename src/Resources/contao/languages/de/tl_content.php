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

$GLOBALS['TL_LANG']['tl_content']['facebookSites'][0] = 'Facebook Seiten';
$GLOBALS['TL_LANG']['tl_content']['facebookSites'][1] = 'W&auml;hlen sie die Facebook Seiten aus, deren Posts ausgegeben werden sollen.';
$GLOBALS['TL_LANG']['tl_content']['maxPosts'][0] = 'Maximalanzahl Elemente';
$GLOBALS['TL_LANG']['tl_content']['maxPosts'][1] = 'Maximale Anzahl der Elemente die ausgegeben werden sollen.';
$GLOBALS['TL_LANG']['tl_content']['sizeFacebook'][0] = $GLOBALS['TL_LANG']['tl_content']['size'][0];
$GLOBALS['TL_LANG']['tl_content']['sizeFacebook'][1] = $GLOBALS['TL_LANG']['tl_content']['size'][1].' <strong>Hinweis:</strong> Diese Funktion findet nur Anwendung, wenn Bilder ins Dateisystem geladen wurden.';

$GLOBALS['TL_LANG']['tl_content']['messageLength'][0] = 'Textlänge';
$GLOBALS['TL_LANG']['tl_content']['messageLength'][1] = 'Definieren Sie, wieviele Zeichen des Textes angezeigt werden sollen. Ein Eintrag von "0" oder ein leeres Feld gibt den kompletten Text aus.';

$GLOBALS['TL_LANG']['tl_content']['showFacebookLinkAlways'][0] = 'Facebook Link immer anzeigen';
\System::loadLanguageFile('tl_facebook_posts');
$GLOBALS['TL_LANG']['tl_content']['showFacebookLinkAlways'][1] = 'Den "'.$GLOBALS['TL_LANG']['tl_facebook_posts']['facebookLinkText'].'" Link immer ausgeben. Unabhängig ob der Text des Posts gek&uuml;rzt wurde.';
$GLOBALS['TL_LANG']['tl_content']['removeHashTag'][0] = 'Hash Tags entfernen';
$GLOBALS['TL_LANG']['tl_content']['removeHashTag'][1] = 'Hash Tags (z.B. #news, #wichtig ...) werden aus dem Teasertext entfernt und nicht auf der Webseite ausgegeben.';
