<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Config;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Spiral\Core\InjectableConfig;

final class PhoneNumberConfig extends InjectableConfig
{
    public const CONFIG = 'libphonenumber';

    protected array $config = [
        'default_region' => PhoneNumberUtil::UNKNOWN_REGION,
        'default_format' => PhoneNumberFormat::E164,
    ];

    public function getDefaultRegion(): string
    {
        return $this->config['default_region'] ?? PhoneNumberUtil::UNKNOWN_REGION;
    }

    public function getDefaultFormat(): int
    {
        return $this->config['default_format'] ?? PhoneNumberFormat::E164;
    }
}
