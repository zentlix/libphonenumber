<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Serializer\Normalizer;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Phone number serialization for Symfony serializer.
 */
class PhoneNumberNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function __construct(
        protected PhoneNumberUtil $phoneNumberUtil,
        protected string $region = PhoneNumberUtil::UNKNOWN_REGION,
        protected int $format = PhoneNumberFormat::E164
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function normalize(mixed $object, string $format = null, array $context = []): string
    {
        return $this->phoneNumberUtil->format($object, $this->format);
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof PhoneNumber;
    }

    /**
     * @throws UnexpectedValueException
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): ?PhoneNumber
    {
        if (null === $data) {
            return null;
        }

        try {
            return $this->phoneNumberUtil->parse($data, $this->region);
        } catch (NumberParseException $e) {
            throw new UnexpectedValueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return PhoneNumber::class === $type && \is_string($data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            PhoneNumber::class => true,
        ];
    }
}
