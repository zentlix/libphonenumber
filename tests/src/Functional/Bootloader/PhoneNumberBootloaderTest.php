<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Tests\Functional\Bootloader;

use libphonenumber\geocoding\PhoneNumberOfflineGeocoder;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberToCarrierMapper;
use libphonenumber\PhoneNumberToTimeZonesMapper;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\ShortNumberInfo;
use Spiral\PhoneNumber\Config\PhoneNumberConfig;
use Spiral\PhoneNumber\Serializer\Normalizer\PhoneNumberNormalizer;
use Spiral\PhoneNumber\Tests\Functional\TestCase;
use Spiral\PhoneNumber\Twig\Extension\PhoneNumberExtension;
use Spiral\Serializer\Symfony\NormalizersRegistryInterface;
use Spiral\Twig\Config\TwigConfig;
use Spiral\Twig\Extension\ContainerExtension;
use Spiral\Validator\Config\ValidatorConfig;

final class PhoneNumberBootloaderTest extends TestCase
{
    public function testPhoneNumberUtilShouldBeBound(): void
    {
        $this->assertContainerBound(PhoneNumberUtil::class, PhoneNumberUtil::class);
    }

    public function testPhoneNumberOfflineGeocoderShouldBeBound(): void
    {
        $this->assertContainerBound(PhoneNumberOfflineGeocoder::class, PhoneNumberOfflineGeocoder::class);
    }

    public function testShortNumberInfoShouldBeBound(): void
    {
        $this->assertContainerBound(ShortNumberInfo::class, ShortNumberInfo::class);
    }

    public function testPhoneNumberToCarrierMapperShouldBeBound(): void
    {
        $this->assertContainerBound(PhoneNumberToCarrierMapper::class, PhoneNumberToCarrierMapper::class);
    }

    public function testPhoneNumberToTimeZonesMapperShouldBeBound(): void
    {
        $this->assertContainerBound(PhoneNumberToTimeZonesMapper::class, PhoneNumberToTimeZonesMapper::class);
    }

    public function testNormalizerShouldBeRegistered(): void
    {
        $container = $this->getApp()->getContainer();
        /** @var NormalizersRegistryInterface $registry */
        $registry = $container->get(NormalizersRegistryInterface::class);

        $this->assertTrue($registry->has(PhoneNumberNormalizer::class));
    }

    public function testValidatorCheckerShouldBeRegistered(): void
    {
        $container = $this->getApp()->getContainer();
        /** @var ValidatorConfig $config */
        $config = $container->get(ValidatorConfig::class);

        $this->assertTrue($config->hasChecker('phone'));
    }

    public function testValidatorAliasShouldBeRegistered(): void
    {
        $container = $this->getApp()->getContainer();
        /** @var ValidatorConfig $config */
        $config = $container->get(ValidatorConfig::class);

        $this->assertSame('phone:isValid', $config->resolveAlias('phone'));
    }

    public function testTwigExtensionShouldBeRegistered(): void
    {
        $this->assertConfigHasFragments(
            TwigConfig::CONFIG,
            [
                'extensions' => [
                    ContainerExtension::class,
                    PhoneNumberExtension::class
                ]
            ]
        );
    }

    public function testDefaultConfigShouldBeDefined(): void
    {
        $this->assertConfigMatches(PhoneNumberConfig::CONFIG, [
            'default_region' => PhoneNumberUtil::UNKNOWN_REGION,
            'default_format' => PhoneNumberFormat::E164
        ]);
    }
}
