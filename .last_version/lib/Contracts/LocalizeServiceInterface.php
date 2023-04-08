<?php

namespace Vasoft\Git\Contracts;

interface LocalizeServiceInterface
{
    public static function getMessage(string $code): string;
}