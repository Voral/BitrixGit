<?php

namespace Vasoft\Git\Exceptions;

class InaccessiblePathException extends ModuleException
{
    public function __construct()
    {
        parent::__construct(ErrorCode::getMessageTemplate(ErrorCode::INACCESSIBLE_PATH), ErrorCode::INACCESSIBLE_PATH);
    }
}