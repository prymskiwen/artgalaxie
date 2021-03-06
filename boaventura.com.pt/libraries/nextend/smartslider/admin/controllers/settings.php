<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class NextendSmartsliderAdminControllerSettings extends NextendSmartsliderAdminController {

    function NextendSmartsliderAdminControllerSettings($key) {
        parent::NextendSmartsliderAdminController($key);

    }

    function defaultAction($form = 'default') {
        if ($this->canDo('core.admin')) {
            $settingsModel = $this->getModel('settings');
            if (NextendRequest::getInt('save')) {
                if ($settingsModel->save()) {
                    header('LOCATION: ' . $this->route('controller=settings'));
                    exit;
                }
            }
            if($form == 'default' && NextendRequest::getVar('action') != $form) $form = 'plugin';
            $this->display($form, 'default');
        }else{
            $this->noaccess();
        }
    }

    function layoutAction() {
        $this->defaultAction('layout');
    }

    function fontAction() {
        $this->defaultAction('font');
    }

    function clearfontsAction() {
        if ($this->canDo('core.admin')) {
            $sliderid = NextendRequest::getInt('sliderid');
            $settingsModel = $this->getModel('settings');
            if ($sliderid) {
                if ($settingsModel->clearfonts($sliderid)) {
                    header('LOCATION: ' . $this->route('controller=settings&view=sliders_settings&action=font&sliderid='.$sliderid));
                    exit;
                }
            }
            $this->display($form, 'default');
        }else{
            $this->noaccess();
        }
    }

    function joomlaAction() {
        if(nextendIsJoomla()) $this->defaultAction('joomla');
    }

    function cacheAction() {
        if(NextendRequest::getInt('refreshcache')){
            $slidersModel = $this->getModel('sliders');
            switch (NextendRequest::getInt('refreshcache')){
                case 1:
                    foreach($slidersModel->getSliders() AS $slider){
                        NextendSmartsliderAdminModelSliders::markChanged($slider['id']);
                    }
                    break;
                case 2:
                    foreach($slidersModel->getSliders() AS $slider){
                        $slidersModel->refreshCache($slider['id']);
                    }
                    break;
            }
            header('LOCATION: ' . $this->route('controller=settings&view=sliders_settings&action=cache'));
            exit;
        }
        $this->display('cache', 'cache');
    }

}
