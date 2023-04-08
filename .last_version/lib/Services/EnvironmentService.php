<?php

namespace Vasoft\Git\Services;

use Bitrix\Main\Diag\Debug;
use Vasoft\Git\Contracts\SessionInterface;
use Vasoft\Git\Exceptions\ServiceNotInitializedException;

class EnvironmentService
{
    private const SESSION_PATH = 'VS_GIT_PATH';
    private string $userName = '';
    private string $currentDir = '';
    private ?CommandService $commandService = null;
    private SessionInterface $sessionManager;

    public function __construct(SessionInterface $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    public function setCommandService(CommandService $commandService): void
    {
        $this->commandService = $commandService;
    }

    public function getCurrentDir(): string
    {
        if ($this->currentDir === '') {
            $this->currentDir = $this->sessionManager->get(self::SESSION_PATH);
            if ($this->currentDir === '') {
                $this->currentDir = $_SERVER['DOCUMENT_ROOT'];
            }
        }
        return $this->currentDir;
    }

    public function getCurrentDirShort(): string
    {
        $parts = explode(DIRECTORY_SEPARATOR, trim($this->getCurrentDir(), DIRECTORY_SEPARATOR));
        return end($parts);
    }

    public function setCurrentDir(string $current): void
    {
        $this->sessionManager->set(self::SESSION_PATH, $current);
        $this->currentDir = '';
    }

    public function getUserName(): string
    {
        if ($this->userName === '') {
            if (!$this->commandService) {
                throw new ServiceNotInitializedException('CommandService');
            }
            $this->userName = $this->commandService->getUserName();
        }
        return $this->userName;
    }
}