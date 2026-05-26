<?php

namespace App\Jobs;

abstract class BaseJob
{
    protected array $payload;

    public function __construct(array $payload = [])
    {
        $this->payload = $payload;
    }

    abstract public function handle(): array;

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getName(): string
    {
        return class_basename(static::class);
    }
}
