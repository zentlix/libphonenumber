<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Tests\Functional;

use Spiral\Bootloader\Attributes\AttributesBootloader;
use Spiral\Bootloader\Security\FiltersBootloader;
use Spiral\Nyholm\Bootloader\NyholmBootloader;
use Spiral\PhoneNumber\Bootloader\PhoneNumberBootloader;
use Spiral\Serializer\Symfony\Bootloader\SerializerBootloader;
use Spiral\Twig\Bootloader\TwigBootloader;
use Spiral\Validation\Bootloader\ValidationBootloader;
use Spiral\Validation\Symfony\Bootloader\ValidatorBootloader as SymfonyValidator;
use Spiral\Validator\Bootloader\ValidatorBootloader as SpiralValidator;

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
            SerializerBootloader::class,
            AttributesBootloader::class,
            NyholmBootloader::class,
            FiltersBootloader::class,
            ValidationBootloader::class,
            SymfonyValidator::class,
            SpiralValidator::class,
        ];
    }
}
