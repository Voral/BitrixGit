<?php

namespace Vasoft\Git\Services;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use PHPUnit\Framework\TestCase;
use Vasoft\Git\Exceptions\InvalidCommandException;
use Vasoft\Modeler\Constructor\Core\Entities\Point;
use Vasoft\Modeler\Constructor\Core\Helpers\LineGeometry;

class SecurityServiceTest extends TestCase
{
    /**
     * @dataProvider allowedDataProvider
     */
    public function testAllowed(string $command, string $expected): void
    {
        $service = new SecurityService();

        $this->assertEquals($expected, $service->secureCommand($command));
    }

    public function allowedDataProvider(): array
    {
        return [
            'escape shell cmd' => ["ls &#;`|*?~<>^()[]{}$\, \x0A \xFF", 'ls \&\#\;\`\|\*\?\~\<\>\^\(\)\[\]\{\}\$\\\\, \\'],
            'cd trim' => [' cd /local ', 'cd /local'],
            'url decode' => ['diff are=green+and+red', 'diff are=green and red'],
            'git' => ['git status', 'git status'],
        ];
    }

    /**
     * @dataProvider notAllowedDataProvider
     */
    public function testNotAllowed(string $command): void
    {
        $this->expectException(InvalidCommandException::class);
        (new SecurityService())->secureCommand($command);
    }

    public function notAllowedDataProvider(): array
    {
        return [
            ['rm /'],
            ['passwd'],
            ['cat'],
            ['su'],
            ['sudo'],
        ];
    }


}
