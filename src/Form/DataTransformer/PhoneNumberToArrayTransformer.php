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
 * @implements DataTransformerInterface<PhoneNumber, array{country: string, number: string}>
 */
final class PhoneNumberToArrayTransformer implements DataTransformerInterface
{
    public function __construct(
        private readonly array $countryChoices
    ) {
    }

    /**
     * @return array{country: string, number: string}
     */
    public function transform(mixed $value): array
    {
        if (null === $value) {
            return ['country' => '', 'number' => ''];
        }

        if (false === $value instanceof PhoneNumber) {
            throw new TransformationFailedException(sprintf('Expected a `%s`.', PhoneNumber::class));
        }

        $util = PhoneNumberUtil::getInstance();

        if (false === \in_array($util->getRegionCodeForNumber($value), $this->countryChoices, true)) {
            throw new TransformationFailedException('Invalid country.');
        }

        return [
            'country' => $util->getRegionCodeForNumber($value) ?? '',
            'number' => $util->format($value, PhoneNumberFormat::NATIONAL),
        ];
    }

    /**
     * @psalm-assert array{country: string, number: string} $value
     */
    public function reverseTransform(mixed $value): ?PhoneNumber
    {
        if (!$value) {
            return null;
        }

        if (!\is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        if ('' === trim($value['number'] ?? '')) {
            return null;
        }

        $util = PhoneNumberUtil::getInstance();

        try {
            $phoneNumber = $util->parse($value['number'], $value['country']);
        } catch (NumberParseException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }

        if (false === \in_array($util->getRegionCodeForNumber($phoneNumber), $this->countryChoices, true)) {
            throw new TransformationFailedException('Invalid country.');
        }

        return $phoneNumber;
    }
}
