<?php
/**
 * @bxnolanginspection
 */

namespace Vasoft\Git\Infrastructure\Settings;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Exception;
use LogicException;
use Vasoft\Git\Contracts\ModuleConfigInterface;

final class ModuleConfig implements ModuleConfigInterface
{
    public const MODULE_ID = 'vasoft.git';
    private static ?ModuleConfig $instance = null;
    private array $arOptions;

    public function getHome(): string
    {
        if (!array_key_exists(Properties::HOME, $this->arOptions)) {
            return '';
        }
        return $this->arOptions[Properties::HOME];
    }

    /**
     * @throws ArgumentNullException
     */
    protected function __construct()
    {
        $this->arOptions = Option::getForModule(self::MODULE_ID);
    }

    /**
     * Делаем приватным
     */
    protected function __clone()
    {
        // Одиночка
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new LogicException('Cannot unserialize a singleton.');
    }

    public static function getInstance(): ModuleConfig
    {
        if (null === self::$instance) {
            self::$instance = new ModuleConfig();
        }
        return self::$instance;
    }

    /**
     * @throws ArgumentNullException
     */
    public function reload(): void
    {
        $this->arOptions = Option::getForModule(self::MODULE_ID);
    }

    /**
     * @param $key
     * @param $value
     * @throws ArgumentOutOfRangeException
     */
    public function set($key, $value): void
    {
        $this->arOptions[$key] = $value;
        Option::set(self::MODULE_ID, $key, $value);
    }

    /**
     * @param array $data
     * @return void
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public function saveFromArray(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
        $this->reload();
    }
}
