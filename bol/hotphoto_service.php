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

/**
 * Photo Service Class.  
 * 
 * @author Zarif Safiullin <zaph.saph@gmail.com>
 * @package ow.plugin.hotphoto.bol
 * @since 1.0
 */
final class HOTPHOTO_BOL_HotphotoService
{
    /**
     * @var PHOTO_BOL_PhotoDao
     */
    private $photoDao;
    /**
     * Class instance
     *
     * @var HOTPHOTO_BOL_HotphotoService
     */
    private static $classInstance;

    /**
     * Class constructor
     *
     */
    private function __construct()
    {
        $this->photoDao = PHOTO_BOL_PhotoDao::getInstance();
    }

    /**
     * Returns class instance
     *
     * @return PHOTO_BOL_PhotoService
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function getNotRatedPhotoByUserId($userId, $sex)
    {
        $sexCond = "";
        if ($sex != 0)
        {
            $sexCond = " AND `question_data`.`questionName`='sex' AND `question_data`.`intValue`={$sex} ";
        }
        
        $sql = "SELECT * FROM `".OW_DB_PREFIX . 'photo'."` AS `photo`
            LEFT JOIN `".OW_DB_PREFIX . 'photo_album'."` AS `album`  ON ( `photo`.`albumId`=`album`.`id`)
            LEFT JOIN `".OW_DB_PREFIX . 'base_rate'."` AS `rate`  ON ( `photo`.`id`=`rate`.`entityId` AND `rate`.`entityType`='photo_rates')
            LEFT JOIN `".OW_DB_PREFIX . "base_question_data` AS `question_data` ON ( `question_data`.`userId` = `album`.`userId`  AND `question_data`.`questionName`='sex' )
            WHERE `album`.`userId`<>{$userId} {$sexCond} AND `photo`.`status`='approved' AND `photo`.`privacy`='everybody' AND ( SELECT COUNT(*) FROM `".OW_DB_PREFIX."base_rate` WHERE `userId`={$userId} AND `entityId`=`photo`.`id` AND `entityType`='photo_rates' ) = 0  ORDER BY RAND() LIMIT 1";

        $photoId = OW::getDbo()->queryForColumn($sql);
        return $this->photoDao->findById($photoId);
    }
}