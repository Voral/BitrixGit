<?php

namespace Vasoft\Git\Exceptions;

class ServiceNotInitializedException extends ModuleException
{
    public function __construct(string $serviceName)
    {
        parent::__construct(
            sprintf(
                ErrorCode::getMessageTemplate(ErrorCode::SERVICE_NOT_INITIALIZED),
                $serviceName
            ),
            ErrorCode::SERVICE_NOT_INITIALIZED
        );
    }
}