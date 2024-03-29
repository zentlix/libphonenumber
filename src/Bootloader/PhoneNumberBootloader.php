<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Bootloader;

use libphonenumber\geocoding\PhoneNumberOfflineGeocoder;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberToCarrierMapper;
use libphonenumber\PhoneNumberToTimeZonesMapper;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\ShortNumberInfo;
use Spiral\Boot\AbstractKernel;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Core\Container;
use Spiral\Core\FactoryInterface as ContainerFactory;
use Spiral\PhoneNumber\Config\PhoneNumberConfig;
use Spiral\PhoneNumber\Serializer\Normalizer\PhoneNumberNormalizer;
use Spiral\PhoneNumber\Twig\Extension\PhoneNumberExtension;
use Spiral\PhoneNumber\Validator\Checker\PhoneNumberChecker;
use Spiral\Serializer\Symfony\NormalizersRegistryInterface;
use Spiral\Twig\Bootloader\TwigBootloader;
use Spiral\Validator\Bootloader\ValidatorBootloader;

final class PhoneNumberBootloader extends Bootloader
{
    protected const BINDINGS = [
        PhoneNumberUtil::class => [self::class, 'initPhoneNumberUtil'],
        PhoneNumberOfflineGeocoder::class => [self::class, 'initPhoneNumberOfflineGeocoder'],
        ShortNumberInfo::class => [self::class, 'initShortNumberInfo'],
        PhoneNumberToCarrierMapper::class => [self::class, 'initPhoneNumberToCarrierMapper'],
        PhoneNumberToTimeZonesMapper::class => [self::class, 'initPhoneNumberToTimeZonesMapper'],
    ];

    public function __construct(
        private readonly ConfiguratorInterface $config
    ) {
    }

    public function init(): void
    {
        $this->initConfig();
    }

    public function boot(
        PhoneNumberConfig $config,
        Container $container,
        ContainerFactory $factory,
        AbstractKernel $kernel
    ): void {
        $this->registerNormalizer($config, $container, $factory);
        $this->registerTwigExtension($container);

        $kernel->booted(fn () => $this->registerValidator($container));
    }

    private function initConfig(): void
    {
        $this->config->setDefaults(
            PhoneNumberConfig::CONFIG,
            [
                'default_region' => PhoneNumberUtil::UNKNOWN_REGION,
                'default_format' => PhoneNumberFormat::E164,
            ]
        );
    }

    private function initPhoneNumberUtil(): PhoneNumberUtil
    {
        return PhoneNumberUtil::getInstance();
    }

    private function initPhoneNumberOfflineGeocoder(): PhoneNumberOfflineGeocoder
    {
        return PhoneNumberOfflineGeocoder::getInstance();
    }

    private function initShortNumberInfo(): ShortNumberInfo
    {
        return ShortNumberInfo::getInstance();
    }

    private function initPhoneNumberToCarrierMapper(): PhoneNumberToCarrierMapper
    {
        return PhoneNumberToCarrierMapper::getInstance();
    }

    private function initPhoneNumberToTimeZonesMapper(): PhoneNumberToTimeZonesMapper
    {
        return PhoneNumberToTimeZonesMapper::getInstance();
    }

    private function registerNormalizer(
        PhoneNumberConfig $config,
        Container $container,
        ContainerFactory $factory
    ): void {
        if (!interface_exists(NormalizersRegistryInterface::class)) {
            return;
        }

        /** @var NormalizersRegistryInterface $registry */
        $registry = $container->get(NormalizersRegistryInterface::class);

        $registry->register($factory->make(PhoneNumberNormalizer::class, [
            'region' => $config->getDefaultRegion(),
            'format' => $config->getDefaultFormat(),
        ]), 670);
    }

    private function registerTwigExtension(Container $container): void
    {
        if (!class_exists(TwigBootloader::class)) {
            return;
        }

        $twig = $container->get(TwigBootloader::class);
        $twig->addExtension(PhoneNumberExtension::class);
    }

    private function registerValidator(Container $container): void
    {
        if (!class_exists(ValidatorBootloader::class)) {
            return;
        }

        $validator = $container->get(ValidatorBootloader::class);
        $validator->addChecker('phone', PhoneNumberChecker::class);
        $validator->addAlias('phone', 'phone::isValid');
    }
}
