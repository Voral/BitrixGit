<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\LoaderException;
use Bitrix\Main\UI\Extension;


Loc::loadMessages(__FILE__);
if ('W' === CMain::GetGroupRight('vasoft.git')) {
    try {
        Extension::load(['ui.vue3', 'vasoft.git']);
        $menu = array(
            array(
                'parent_menu' => 'global_menu_settings',
                'sort' => 2,
                'text' => Loc::getMessage('VASOFT_GIT_MENU'),
                'title' => Loc::getMessage('VASOFT_GIT_MENU'),
                'url' => 'vasoft_git_console.php',
                'module_id' => 'vasoft.git',
                'items_id' => 'vasoft_git_console',
                'icon' => 'vasoft-git',
            ),
        );
        return $menu;
    } catch (LoaderException $e) {
        // @todo Запись в журнал системы
    }
}
return false;

