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
 * Photo base action controller
 *
 * @author Zarif Safiullin <zaph.saph@gmail.com>
 * @package ow.plugin.hotphoto.controllers
 * @since 1.0
 */
class HOTPHOTO_CTRL_Rate extends OW_ActionController {

    const ENTITY_TYPE = 'photo_rates';
    
    public function __construct() {
        parent::__construct();
    }

    /**
     * Rate hot or not photo action
     *
     * @param array $params
     */
    public function index(array $params) {
        $language = OW::getLanguage();

        OW::getNavigation()->activateMenuItem(OW_Navigation::MAIN, 'hotphoto', 'hotphoto');

        if (!OW::getPluginManager()->isPluginActive('photo')) {
            $this->assign('service_not_available', $language->text('hotphoto', 'service_not_available'));
            return;
        } else {
            $this->assign('service_not_available', false);
        }

        $sex = 0;
        if (!empty($params['sex'])) {
            $sex = $params['sex'];
        }

        $randomPhoto = new HOTPHOTO_CMP_RandomPhoto(array('sex' => $sex));
        $this->addComponent('randomPhoto', $randomPhoto);
    }

    public function refreshPhoto() {
        $photoService = PHOTO_BOL_PhotoService::getInstance();
        $hotphotoService = HOTPHOTO_BOL_HotphotoService::getInstance();
        $photo = $hotphotoService->getNotRatedPhotoByUserId(OW::getUser()->getId(), $_POST['sex']);

        if (!$photo) {
            exit( json_encode(array('noPhoto' => true)) );
            return;
        }

        $ownerId = $photoService->findPhotoOwner($photo->id);
        $imagePath = $photoService->getPhotoUrl($photo->id);
        $totalScoreCmp = new BASE_CMP_TotalScore($photo->id, HOTPHOTO_CTRL_Rate::ENTITY_TYPE);

        exit( json_encode(array('totalScoreCmp' => $totalScoreCmp->render(), 'noPhoto' => false, 'ownerId' => $ownerId, 'entityId' => $photo->id, 'imagePath' => $imagePath)) );
    }

    public function getNextPhoto() {
        $service = BOL_RateService::getInstance();

        $entityId = (int) $_POST['entityId'];
        $entityType = HOTPHOTO_CTRL_Rate::ENTITY_TYPE;
        $rate = (int) $_POST['rate'];
        $ownerId = (int) $_POST['ownerId'];
        $userId = OW::getUser()->getId();

        if (!OW::getUser()->isAuthenticated()) {
            exit( json_encode(array('errorMessage' => OW::getLanguage()->text('base', 'rate_cmp_auth_error_message'))) );
        }

        if ($userId === $ownerId) {
            exit( json_encode(array('errorMessage' => OW::getLanguage()->text('base', 'rate_cmp_owner_cant_rate_error_message'))) );
        }

        if (false) {
            exit( json_encode(array('errorMessage' => 'Auth error')) );
        }

        $rateItem = $service->findRate($entityId, $entityType, $userId);

        if ($rateItem === null) {
            $rateItem = new BOL_Rate();
            $rateItem->setEntityId($entityId)->setEntityType($entityType)->setUserId($userId)->setActive(true);
        }

        $rateItem->setScore($rate)->setTimeStamp(time());

        $service->saveRate($rateItem);

        /**/
        
        $this->refreshPhoto();
        
        /*
          $photoService = PHOTO_BOL_PhotoService::getInstance();
          $hotphotoService = HOTPHOTO_BOL_HotphotoService::getInstance();
          $photo = $hotphotoService->getNotRatedPhotoByUserId( OW::getUser()->getId(), $_POST['sex'] );

          if ( !$photo )
          {
          exit( json_encode(array('noPhoto'=>true)) );
          }

          $entityId = $photo->id;
          $ownerId = $photoService->findPhotoOwner($photo->id);
          $imagePath = $photoService->getPhotoUrl($photo->id);
          $totalScoreCmp = new BASE_CMP_TotalScore($entityId, $entityType);

          exit( json_encode(array('totalScoreCmp' => $totalScoreCmp->render(),'noPhoto'=>false, 'ownerId'=>$ownerId, 'entityId'=>$entityId, 'imagePath'=>$imagePath)) );
         */
    }

}

