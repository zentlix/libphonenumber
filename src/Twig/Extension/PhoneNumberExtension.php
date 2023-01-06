<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Twig\Extension;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use Spiral\PhoneNumber\Exception\InvalidArgumentException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

final class PhoneNumberExtension extends AbstractExtension
{
    public function __construct(
        private readonly PhoneNumberUtil $phoneNumberUtil
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('phone_number_format', [$this, 'format']),
            new TwigFilter(
                'phone_number_format_out_of_country_calling_number',
                [$this, 'formatOutOfCountryCallingNumber']
            ),
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest('phone_number_of_type', [$this, 'isType']),
        ];
    }

    /**
     * @internal
     */
    public function format(PhoneNumber $phoneNumber, int|string $format = PhoneNumberFormat::INTERNATIONAL): string
    {
        if (true === \is_string($format)) {
            $constant = '\libphonenumber\PhoneNumberFormat::'.$format;

            if (false === \defined($constant)) {
                throw new InvalidArgumentException(sprintf(
                    'The format must be either a constant value or name in `%s`.',
                    PhoneNumberFormat::class
                ));
            }

            $format = \constant('\libphonenumber\PhoneNumberFormat::'.$format);
        }

        return $this->phoneNumberUtil->format($phoneNumber, $format);
    }

    /**
     * @internal
     */
    public function formatOutOfCountryCallingNumber(PhoneNumber $phoneNumber, mixed $regionCode): string
    {
        return $this->phoneNumberUtil->formatOutOfCountryCallingNumber($phoneNumber, $regionCode);
    }

    /**
     * @internal
     */
    public function isType(PhoneNumber $phoneNumber, int|string $type = PhoneNumberType::UNKNOWN): bool
    {
        if (true === \is_string($type)) {
            $constant = '\libphonenumber\PhoneNumberType::'.$type;

            if (false === \defined($constant)) {
                throw new InvalidArgumentException(sprintf(
                    'The format must be either a constant value or name in `%s`.',
                    PhoneNumberType::class
                ));
            }

            $type = \constant('\libphonenumber\PhoneNumberType::'.$type);
        }

        return $this->phoneNumberUtil->getNumberType($phoneNumber) === $type;
    }
}
