<?php

namespace Vasoft\Git\Exceptions;

class InvalidCommandException extends ModuleException
{
    public function __construct()
    {
        parent::__construct(ErrorCode::getMessageTemplate(ErrorCode::INVALID_COMMAND), ErrorCode::INVALID_COMMAND);
    }
}