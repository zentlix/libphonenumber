<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Tests\Functional;

use Spiral\PhoneNumber\Bootloader\PhoneNumberBootloader;
use Spiral\Serializer\Symfony\Bootloader\SerializerBootloader;
use Spiral\Twig\Bootloader\TwigBootloader;

abstract class TestCase extends \Spiral\Testing\TestCase
{
    public function rootDirectory(): string
    {
        return __DIR__ . '/../';
    }

    public function defineBootloaders(): array
    {
        return [
            TwigBootloader::class,
            PhoneNumberBootloader::class,
            SerializerBootloader::class
        ];
    }
}
