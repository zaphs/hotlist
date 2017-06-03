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
 * Random photo
 *
 * @author Zarif Safiullin <zaph.saph@gmail.com>
 * @package ow.plugin.hotphoto.components
 * @since 1.0
 */
class HOTPHOTO_CMP_RandomPhoto extends OW_Component
{
    /**
     * @var PHOTO_BOL_PhotoService 
     */
    private $photoService;
    
    /**
     * @var HOTPHOTO_BOL_HotphotoService 
     */
    private $hotphotoService;
    
    /**
     *
     * @var BOL_RateService 
     */
    private $rateService;

    public function __construct( array $params)
    {
        parent::__construct();
        
        $language = OW::getLanguage();
        
        $this->photoService = PHOTO_BOL_PhotoService::getInstance();
        $this->hotphotoService = HOTPHOTO_BOL_HotphotoService::getInstance();


        $sexes = BOL_QuestionValueDao::getInstance()->findQuestionValues('sex');
        $allSex = array();
        foreach($sexes as $id=>$sex)
        {
            $allSex[$id]['value']=$sex->value;
            $allSex[$id]['name']=OW::getLanguage()->text('base', 'questions_question_sex_value_'.$sex->value);
        }
        
        $this->assign('allSex', $allSex);
        
        $this->assign('selectedSex', $params['sex']);
        
        $route = substr(OW::getRouter()->urlForRoute('rate_hot_photo'), 0, -1);
        $this->assign('hotphoto_url', $route);        
        
        $photo = $this->hotphotoService->getNotRatedPhotoByUserId( OW::getUser()->getId(), $params['sex'] );

        if ( !$photo )
        {
            $this->assign('no_photos', true);
            $this->assign('label', OW::getLanguage()->text('base', 'empty_list'));
            return;
        }
        else
        {
            $this->assign('no_photos', false);
        }
        
        $this->assign('label', OW::getLanguage()->text('hotphoto', 'rate_photo'));
        
        $contentOwner = $this->photoService->findPhotoOwner($photo->id);
        $rate = new HOTPHOTO_CMP_Rate('photo', 'photo_rates', $photo->id, $contentOwner, $params['sex']);
        
        $this->assign('photo', $photo);
        $this->assign('url', $this->photoService->getPhotoUrl($photo->id));
        
        $toolbar = array();

        if ( (int) OW::getConfig()->getValue('photo', 'store_fullsize') && $photo->hasFullsize )
        {
            array_push($toolbar, array(
                'href' => $this->photoService->getPhotoFullsizeUrl($photo->id),
                'label' => $language->text('photo', 'view_fullsize')
            ));
        }

        array_push($toolbar, array(
            'href' => 'javascript://',
            'id' => 'btn-photo-flag',
            'label' => $language->text('base', 'flag')
        ));
        
        $this->assign('toolbar', $toolbar);
        $this->addComponent('rate', $rate);
    }
}