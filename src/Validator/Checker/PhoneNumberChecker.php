<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Validator\Checker;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use Spiral\PhoneNumber\Config\PhoneNumberConfig;
use Spiral\Validator\AbstractChecker;

final class PhoneNumberChecker extends AbstractChecker
{
    public function __construct(
        private readonly PhoneNumberConfig $config
    ) {
    }

    public const MESSAGES = [
        'isValid' => '[[This value is not a valid phone number.]]',
    ];

    public function isValid(string $number): bool
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            /** @var PhoneNumber $phoneNumber */
            $phoneNumber = $phoneUtil->parse($number, $this->config->getDefaultRegion());

            return $phoneUtil->isValidNumber($phoneNumber);
        } catch (\Throwable) {
            return false;
        }
    }
}
