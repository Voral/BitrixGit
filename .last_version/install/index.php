<?php /** @noinspection DuplicatedCode */

/**
 * @noinspection ReturnTypeCanBeDeclaredInspection
 * @noinspection ReturnTypeCanBeDeclaredInspection
 * @noinspection AccessModifierPresentedInspection
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class vasoft_git extends CModule
{
    var $MODULE_ID = "vasoft.git";

    private static array $exclusionAdminFiles = array(
        '.',
        '..',
        'menu.php'
    );

    public function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__ . '/version.php');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('VASOFT_GIT_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('VASOFT_GIT_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = 'VASoft';
        $this->PARTNER_URI = 'https://va-soft.ru/';

        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        $this->MODULE_GROUP_RIGHTS = 'Y';
    }

    public function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
        $this->installFiles();
    }

    public function DoUninstall()
    {
        $this->unInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function InstallFiles()
    {
        CopyDirFiles($this->getPath()."/install/bitrix/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
        $path = $this->GetPath() . '/admin/';
        $pathDR = $this->GetPath(true) . '/admin/';
        if (Bitrix\Main\IO\Directory::isDirectoryExists($path) && $dir = opendir($path)) {
            while (false !== $item = readdir($dir)) {
                if (in_array($item, self::$exclusionAdminFiles, true)) {
                    continue;
                }
                $subName = str_replace('.', '_', $this->MODULE_ID);
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $subName . '_' . $item, '<' . '? require($_SERVER["DOCUMENT_ROOT"]."' . $pathDR . $item . '");');
            }
            closedir($dir);
        }
    }

    public function UnInstallFiles()
    {
        DeleteDirFiles($this->getPath() . "/install/bitrix/images/vasoft.git", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/images/vasoft.git");
        if (Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->getPath() . '/admin') && $dir = opendir($path)) {
            while (false !== $item = readdir($dir)) {
                if (in_array($item, self::$exclusionAdminFiles, true)) {
                    continue;
                }
                $subName = str_replace('.', '_', $this->MODULE_ID);
                \Bitrix\Main\IO\File::deleteFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $subName . '_' . $item);
            }
            closedir($dir);
        }
    }

    public function getPath($notDocumentRoot = false)
    {
        return ($notDocumentRoot)
            ? preg_replace('#^(.*)/(local|bitrix)/modules#', '/$2/modules', dirname(__DIR__))
            : dirname(__DIR__);
    }
}