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

namespace Postyou\ContaoFacebookConnectorBasicBundle\ContaoManager;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;



class Plugin implements BundlePluginInterface
{
  /**
   * Plugin for the Contao Manager.
   *
   * @author Mario Gienapp
   */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create('Postyou\ContaoFacebookConnectorBasicBundle\PostyouContaoFacebookConnectorBasicBundle')
                            ->setLoadAfter(['Contao\CoreBundle\ContaoCoreBundle','Contao\NewsBundle\ContaoNewsBundle', 'Contao\CalendarBundle\ContaoCalendarBundle'])
                            ->setReplace(['contao-facebook-connector_basic']),
        ];
    }
}
