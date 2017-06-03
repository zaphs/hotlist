<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.

 * ---
 * Copyright (c) 2011, Oxwall Foundation
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and
 *  the following disclaimer.
 *
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *  the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  - Neither the name of the Oxwall Foundation nor the names of its contributors may be used to endorse or promote products
 *  derived from this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
$plugin = OW::getPluginManager()->getPlugin('hotphoto');

OW::getRouter()->addRoute(new OW_Route('rate_hot_photo', 'hotphoto/rate/:sex', 'HOTPHOTO_CTRL_Rate', 'index', array('sex'=>array(OW_Route::PARAM_OPTION_DEFAULT_VALUE => 0)) ));
OW::getRouter()->addRoute(new OW_Route('rate_next_photo', 'hotphoto/rate/get-next-photo', 'HOTPHOTO_CTRL_Rate', 'getNextPhoto'));
OW::getRouter()->addRoute(new OW_Route('refresh_photo', 'hotphoto/rate/refresh-photo', 'HOTPHOTO_CTRL_Rate', 'refreshPhoto'));

function hotphoto_is_photo_active( BASE_CLASS_EventCollector $event )
{
    if ( !OW::getPluginManager()->isPluginActive('photo') )
    {
        $language = OW::getLanguage();

        $event->add($language->text('hotphoto', 'error_photo_not_installed'));
    }
}
OW::getEventManager()->bind('admin.add_admin_notification', 'hotphoto_is_photo_active');