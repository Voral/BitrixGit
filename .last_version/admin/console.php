<?php
/**
 * @global CMain $APPLICATION
 */

use Bitrix\Main\LoaderException;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Localization\Loc;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
Loc::loadMessages(__FILE__);

$userRight = CMain::GetGroupRight("vasoft.git");

if ($userRight === "D") {
    $APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}
try {
    Extension::load(['ui.vue3', 'vasoft.git']);
} catch (LoaderException $e) {
}
$APPLICATION->SetTitle(Loc::getMessage('VASOFT_GIT_TITLE'));
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
    <div id="application"></div>
    <script>
        const gitConsole = new BX.Vasoft.GitConsole('#application');
        gitConsole.start();
    </script>
<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");