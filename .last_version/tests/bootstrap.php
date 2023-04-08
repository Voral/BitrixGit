<?php
/** @noinspection PhpDefineCanBeReplacedWithConstInspection */
// vendor/bin/phpunit -c tests/phpunit.xml --testsuite MyTestSuit
$_SERVER["DOCUMENT_ROOT"] = dirname(__DIR__, 4) . "/";
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
file_put_contents('test.txt',$DOCUMENT_ROOT);

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define('BX_NO_ACCELERATOR_RESET', true);
define('CHK_EVENT', true);

require_once($_SERVER["DOCUMENT_ROOT"] . "bitrix/modules/main/include/prolog_before.php");
echo Bitrix\Main\Loader::includeModule('vasoft.git'),PHP_EOL;
echo '-----',PHP_EOL;
