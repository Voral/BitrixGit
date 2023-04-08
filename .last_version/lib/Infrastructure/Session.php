<?php

namespace Vasoft\Git\Infrastructure;

use Vasoft\Git\Contracts\SessionInterface;

class Session
    implements SessionInterface
{
    private const LOCAL_SESSION_CACHE = 'LOCAL_SESSION_CACHE';
    private \Bitrix\Main\Data\LocalStorage\SessionLocalStorage $storage;

    public function __construct()
    {
        $this->storage = \Bitrix\Main\Application::getInstance()->getLocalSession(self::LOCAL_SESSION_CACHE);
    }

    public function get(string $code): string
    {
        return $this->storage[$code] ?? '';
    }

    public function set(string $code, string $value): void
    {
        $this->storage->set($code, $value);
    }
}