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

use Facebook\PersistentData\PersistentDataInterface;
use Contao\Session;

class ContaoPersistentDataHandler implements PersistentDataInterface
{

    /**
     *
     * @var string Prefix to use for session variables.
     */
    protected $sessionPrefix = 'FBRLH_';

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $_SESSION['BE_DATA'][$this->sessionPrefix . $key];
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        $_SESSION['BE_DATA'][$this->sessionPrefix . $key] = $value;
    }
}
