<?php

namespace Vasoft\Git\Services;

use Vasoft\Git\Exceptions\InvalidCommandException;

class SecurityService
{
    /** @var string[] Маски разрешенных команд */
    public static array $allowed = ['ls*', 'git*', 'cd*', 'diff*'];

    /**
     * @param string $command
     * @return string
     * @throws InvalidCommandException
     */
    public function secureCommand(string $command): string
    {
        $result = trim(escapeshellcmd(urldecode($command)));
        if ($result === '' || !$this->searchCommand($result, self::$allowed)) {
            throw new InvalidCommandException();
        }
        return $result;
    }

    /**
     * Поиск команды по маскам
     * @param string $userCommand Команда переданная пользователем
     * @param array $commands Маски команд для поиска
     * @return bool
     */
    private function searchCommand(string $userCommand, array $commands): bool
    {
        foreach ($commands as $value) {
            $pattern = '/^' . str_replace('\*', '(.*?)', preg_quote($value, '/')) . '$/i';
            if (preg_match($pattern, $userCommand)) {
                return true;
            }
        }
        return false;
    }

}
