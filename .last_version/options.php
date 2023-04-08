<?php

/**
 * @global CMain $APPLICATION
 * @var $mid
 * @noinspection PhpUnhandledExceptionInspection
 * @noinspection PhpMultipleClassDeclarationsInspection
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main;
use Vasoft\Git\Infrastructure\Settings\ModuleConfig;
use Vasoft\Git\Infrastructure\Settings\FormConfig;


Loc::loadMessages(__FILE__);
$module_id = "vasoft.git";
$rights = CMain::GetGroupRight($module_id);
if ($rights < "R") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

Main\Loader::includeModule($module_id);
$config = ModuleConfig::getInstance();
$settingsConfig = new FormConfig($config);
$request = Main\Context::getCurrent()->getRequest();
if ($request->isPost()) {
    try {
        if ($rights < "W") {
            throw new Main\AccessDeniedException();
        }
        if (!check_bitrix_sessid()) {
            throw new Main\ArgumentException("Bad sessid.");
        }
        if ($request->getPost("restore") !== null) {
            COption::RemoveOption($module_id);
            $v1 = "id";
            $v2 = "asc";
            $z = CGroup::GetList($v1, $v2, array("ACTIVE" => "Y", "ADMIN" => "N"));
            while ($zr = $z->Fetch()) {
                CMain::DelGroupRight($module_id, array($zr["ID"]));
            }
        } else {
            $config->saveFromArray($request->getPostList()->getValues());
        }
    } catch (Exception $exception) {
        CAdminMessage::ShowMessage($exception->getMessage());
    }

}
$aTabs = $settingsConfig->getTabs();
$aTabs[] = array("DIV" => "bx-rights", "TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"), "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS"));

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>
<form method="post"
      action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($mid) ?>&lang=<?= LANGUAGE_ID ?>">
    <?= bitrix_sessid_post() ?>
    <?php
    foreach ($aTabs as $tab):
        /** @noinspection DisconnectedForeachInstructionInspection */
        $tabControl->BeginNextTab();
        if ($tab['DIV'] === 'bx-rights') {
            require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php");
        } else {
            $arFields = $settingsConfig->getFields($tab['DIV']);
            foreach ($arFields as $field) {
                echo $field->render();
            }
        }
    endforeach;
    $tabControl->Buttons();
    ?>
    <script>
        function RestoreDefaults() {
            if (confirm('<?= AddSlashes(Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
                window.location = "<?= $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?=LANGUAGE_ID?>&mid=<?= urlencode($mid)?>&<?=bitrix_sessid_get()?>";
        }
    </script>
    <input <?php
    if ($rights < "W") {
        echo "disabled";
    } ?> type="submit" name="Update"
         value="<?= Loc::getMessage("MAIN_SAVE") ?>">
    <input type="hidden" name="Update" value="Y">
    <input <?php
    if ($rights < "W") {
        echo "disabled";
    } ?> type="button"
         title="<?= Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>"
         OnClick="RestoreDefaults();" value="<?= Loc::getMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>">
    <?php $tabControl->End(); ?>
</form>

