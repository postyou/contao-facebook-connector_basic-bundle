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

abstract class ConnectionType
{
    const POST_GET = 'PostGet';

    const POST_PUBLISH = 'PostPublish';

    const GALLERY_GET = 'GalleryGet';

    const GALLERY_PUBLISH = 'GalleryPublish';

    const EVENTS = 'Events';
}
