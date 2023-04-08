<?php

namespace Vasoft\Git\Infrastructure\Services;

use Bitrix\Main\Localization\Loc;
use Vasoft\Git\Contracts\LocalizeServiceInterface;
Loc::loadMessages(__FILE__);

class LocalizeService
    implements LocalizeServiceInterface
{
    public static function getMessage(string $code): string
    {
        return Loc::getMessage($code) ?? '';
    }
}