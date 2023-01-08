<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Tests\Unit\Config;

use libphonenumber\PhoneNumberFormat;
use PHPUnit\Framework\TestCase;
use Spiral\PhoneNumber\Config\PhoneNumberConfig;

final class PhoneNumberConfigTest extends TestCase
{
    public function testGetDefaultRegion(): void
    {
        $config = new PhoneNumberConfig(['default_region' => 'US']);

        $this->assertSame('US', $config->getDefaultRegion());
    }

    public function testGetDefaultFormat(): void
    {
        $config = new PhoneNumberConfig(['default_format' => PhoneNumberFormat::INTERNATIONAL]);

        $this->assertSame(PhoneNumberFormat::INTERNATIONAL, $config->getDefaultFormat());
    }
}
