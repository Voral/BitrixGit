<?php

namespace Vasoft\Git\Exceptions;

use Bitrix\Main\Diag\Debug;
use Vasoft\Git\Infrastructure\Services\LocalizeService;

/**
 * @todo Перейти на enum
 */
class ErrorCode
{
    public const INVALID_COMMAND = 5000;
    public const INACCESSIBLE_PATH = 5001;
    public const SERVICE_NOT_INITIALIZED = 5002;

    public static function getMessageTemplate(int $errorCode): string
    {
        switch ($errorCode) {
            case self::INVALID_COMMAND:
                return LocalizeService::getMessage('VS_GIT_ERROR_INVALID_COMMAND');
            case self::INACCESSIBLE_PATH:
                return LocalizeService::getMessage('VS_GIT_ERROR_INACCESSIBLE_PATH');
            case self::SERVICE_NOT_INITIALIZED:
                return LocalizeService::getMessage('VS_GIT_ERROR_SERVICE_NOT_INITIALIZED');
        }
        return '';
    }
}