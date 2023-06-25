<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Tests\Unit\Serializer\Normalizer;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Spiral\PhoneNumber\Serializer\Normalizer\PhoneNumberNormalizer;

final class PhoneNumberNormalizerTest extends TestCase
{
    #[DataProvider('supportsNormalizationDataProvider')]
    public function testSupportsNormalization(mixed $data, bool $expected): void
    {
        $normalizer = new PhoneNumberNormalizer(PhoneNumberUtil::getInstance());

        $this->assertSame($expected, $normalizer->supportsNormalization($data));
    }

    #[DataProvider('supportsDenormalizationDataProvider')]
    public function testSupportsDenormalization(mixed $data, string $type, bool $expected): void
    {
        $normalizer = new PhoneNumberNormalizer(PhoneNumberUtil::getInstance());

        $this->assertSame($expected, $normalizer->supportsDenormalization($data, $type));
    }

    public static function supportsNormalizationDataProvider(): \Traversable
    {
        yield [new PhoneNumber(), true];
        yield [new \stdClass(), false];
        yield [null, false];
        yield [true, false];
        yield [1, false];
        yield ['foo', false];
    }

    public static function supportsDenormalizationDataProvider(): \Traversable
    {
        yield ['string', PhoneNumber::class, true];
        yield ['string', \stdClass::class, false];
        yield [true, PhoneNumber::class, false];
        yield [1, PhoneNumber::class, false];
        yield [[], PhoneNumber::class, false];
        yield [null, PhoneNumber::class, false];
    }
}
