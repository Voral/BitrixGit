<?php

namespace Vasoft\Git\Services;

use Bitrix\Main\Diag\Debug;
use Bitrix\Main\Localization\Loc;
use Vasoft\Git\Contracts\ModuleConfigInterface;
use Vasoft\Git\Dto\ResultDto;
use Vasoft\Git\Exceptions\InaccessiblePathException;
use Vasoft\Git\Exceptions\InvalidCommandException;

class CommandService
{
    /**
     * Массив преобразования команд.
     * Можно использовать * для любого символа и $1 для подстановки замен
     * Обычно достаточно '*' => '$1'.
     * Если вам необходимы преобразования, примеры:
     *    'move * *' => 'mv $1 $2'
     *    'git*' => '/usr/bin/local/git $1'
     *    '*' => '$1' - при этом должно быть последним в списке
     */
    private static array $commandTransformation = [
        '*' => '$1',
    ];
    private ModuleConfigInterface $config;
    private EnvironmentService $environmentService;

    public function __construct(
        EnvironmentService    $environmentService,
        ModuleConfigInterface $config
    )
    {
        $this->config = $config;
        $this->environmentService = $environmentService;
    }

    /**
     * @param string $command
     * @return ResultDto
     * @throws InaccessiblePathException
     * @throws InvalidCommandException
     */
    public function execute(string $command): ResultDto
    {
        if ($this->executeChangeDir($command)) {
            return new ResultDto();
        }
        $command = $this->transform($command);
        $strCommand = "cd " . $this->environmentService->getCurrentDir() . " && ";
        $strHome = $this->config->getHome();
        if ($strHome !== '') {
            $strCommand .= ("HOME=" . $strHome . " ");
        }
        $strCommand .= $command;
        return $this->executeCommand($strCommand);
    }

    /**
     * @param string $command
     * @return bool
     * @throws InaccessiblePathException
     */
    public function executeChangeDir(string $command): bool
    {
        $matches = [];
        if (1 === preg_match('/^cd\s+(?<path>.+?)$/i', $command, $matches)) {
            $newDir = $matches['path'];
            $newDir = '/' === $newDir[0] ? $newDir : $this->environmentService->getCurrentDir() . '/' . $newDir;
            $newDir = str_replace('//', '/', $newDir);
            if (is_dir($newDir)) {
                $this->environmentService->setCurrentDir(realpath($newDir));
                return true;
            }
            throw new InaccessiblePathException();
        }
        return false;
    }

    /**
     * @param $userCommand
     * @return string
     * @throws InvalidCommandException
     */
    private function transform($userCommand): string
    {
        foreach (self::$commandTransformation as $pattern => $format) {
            $pattern = '/^' . str_replace('\*', '(.*?)', preg_quote($pattern, '/')) . '$/i';
            if (preg_match($pattern, $userCommand)) {
                return preg_replace($pattern, $format, $userCommand);
            }
        }
        throw new InvalidCommandException();
    }

    /**
     * Непосредственное выполнение команды
     * @param string $command Команда на выполнение
     */
    private function executeCommand(string $command): ResultDto
    {
        $descriptors = [
            0 => ["pipe", "r"], // stdin - read channel
            1 => ["pipe", "w"], // stdout - write channel
            2 => ["pipe", "w"], // stdout - error channel
            3 => ["pipe", "r"], // stdin - This is the pipe we can feed the password into
        ];

        $process = proc_open($command, $descriptors, $pipes);

        if (!is_resource($process)) {
            die(Loc::getMessage('VASOFT_GIT_ERROR_PROCOPEN'));
        }

        $result = new ResultDto();
        // Nothing to push to input.
        fclose($pipes[0]);

        $result->output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $result->error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        // TODO: Write passphrase in pipes[3].
        fclose($pipes[3]);

        // Close all pipes before proc_close!
        $result->code = proc_close($process);

        return $result;
    }

    public function getUserName(): string
    {
        $result = $this->executeCommand('whoami');
        return trim($result->code === 0 ? $result->output : $result->error);
    }
}
