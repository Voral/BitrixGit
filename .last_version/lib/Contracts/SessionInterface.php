<?php

namespace Vasoft\Git\Contracts;

interface SessionInterface
{
    public function get(string $code): string;

    public function set(string $code, string $value): void;
}