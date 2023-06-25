<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Form\DataTransformer;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<PhoneNumber, string>
 */
final class PhoneNumberToStringTransformer implements DataTransformerInterface
{
    public function __construct(
        private readonly string $defaultRegion = PhoneNumberUtil::UNKNOWN_REGION,
        private readonly int $format = PhoneNumberFormat::INTERNATIONAL
    ) {
    }

    public function transform(mixed $value): string
    {
        if (null === $value) {
            return '';
        }

        if (false === $value instanceof PhoneNumber) {
            throw new TransformationFailedException(sprintf('Expected a `%s`.', PhoneNumber::class));
        }

        $util = PhoneNumberUtil::getInstance();

        if (PhoneNumberFormat::NATIONAL === $this->format) {
            return $util->formatOutOfCountryCallingNumber($value, $this->defaultRegion);
        }

        return $util->format($value, $this->format);
    }

    public function reverseTransform(mixed $value): ?PhoneNumber
    {
        if (!$value && '0' !== $value) {
            return null;
        }

        $util = PhoneNumberUtil::getInstance();

        try {
            return $util->parse($value, $this->defaultRegion);
        } catch (NumberParseException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
