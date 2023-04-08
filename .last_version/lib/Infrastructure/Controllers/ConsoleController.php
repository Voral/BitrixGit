<?php /** @noinspection PhpMissingReturnTypeInspection */

namespace Vasoft\Git\Infrastructure\Controllers;

use Bitrix\Main\Engine\JsonController;
use Vasoft\Git\Infrastructure\Controllers\ActionFilters\RightsFilter;
use Vasoft\Git\Infrastructure\Mappers\CommandResultMapper;
use Vasoft\Git\Infrastructure\Session;
use Vasoft\Git\Services\CommandService;
use Vasoft\Git\Services\EnvironmentService;
use Vasoft\Git\Infrastructure\Settings\ModuleConfig;
use Vasoft\Git\Services\SecurityService;

class ConsoleController extends JsonController
{
    /**
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function getDefaultPreFilters()
    {
        return array_merge(
            [
                new RightsFilter()
            ],
            parent::getDefaultPreFilters()
        );
    }

    public function environmentAction(): array
    {
        $environment = new EnvironmentService(new Session());
        $command = new CommandService($environment, ModuleConfig::getInstance());
        $environment->setCommandService($command);
        return [
            'user' => $environment->getUserName(),
            'path' => $environment->getCurrentDirShort(),
            'autocomplete' => [
                '^\w*$' => ['cd', 'ls', 'git', 'diff', 'more', 'grep'],
                '^git \w*$' => ['status', 'push', 'pull', 'add', 'bisect', 'branch', 'checkout', 'clone', 'commit', 'diff', 'fetch', 'grep', 'init', 'log', 'merge', 'mv', 'rebase', 'reset', 'rm', 'show', 'tag', 'remote'],
                '^git \w* .*' => ['HEAD', 'origin', 'master', 'production', 'develop', 'rename', '--cached', '--global', '--local', '--merged', '--no-merged', '--amend', '--tags', '--no-hardlinks', '--shared', '--reference', '--quiet', '--no-checkout', '--bare', '--mirror', '--origin', '--upload-pack', '--template=', '--depth', '--help'],
            ]
        ];
    }

    public function executeAction(string $command): array
    {
        $environment = new EnvironmentService(new Session());
        $commandService = new CommandService($environment, ModuleConfig::getInstance());
        $environment->setCommandService($commandService);
        $command = (new SecurityService())->secureCommand($command);
        $result = (new CommandResultMapper())->dtoToJson($commandService->execute($command));
        $result['path'] = $environment->getCurrentDirShort();
        return $result;
    }
}