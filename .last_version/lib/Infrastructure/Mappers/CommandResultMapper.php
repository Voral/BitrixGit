<?php

namespace Vasoft\Git\Infrastructure\Mappers;

use Vasoft\Git\Dto\ResultDto;

class CommandResultMapper
{
    public function dtoToJson(ResultDto $dto): array
    {
        return [
            'code' => $dto->code,
            'error' => $dto->error,
            'output' => $dto->output
        ];
    }
}