<?php
/**
 * @bxnolanginspection
 */

namespace Vasoft\Git\Infrastructure\Settings;

use Bitrix\Main\Localization\Loc;

/**
 * @todo переделать на enum
 */
final class Properties
{
    public const HOME = 'HOME';

    public static function description(string $key): string
    {
        return $key === self::HOME ? Loc::getMessage("VS_GIT_HOME") : '';
    }
}
