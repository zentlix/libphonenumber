<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Tests\Functional\Serializer\Normalizer;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use Spiral\PhoneNumber\Tests\Functional\TestCase;
use Spiral\Serializer\SerializerManager;

final class PhoneNumberNormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $utils = PhoneNumberUtil::getInstance();

        /** @var SerializerManager $manager */
        $manager = $this->getContainer()->get(SerializerManager::class);

        $this->assertSame(
            json_encode('+16502530000', JSON_THROW_ON_ERROR),
            $manager->getSerializer('json')->serialize($utils->parse('+1 650 253 0000', 'US'))
        );
    }

    public function testDenormalize(): void
    {
        $utils = PhoneNumberUtil::getInstance();

        /** @var SerializerManager $manager */
        $manager = $this->getContainer()->get(SerializerManager::class);

        $this->assertEquals(
            $utils->parse('+1 650 253 0000', 'US'),
            $manager->getSerializer('json')->unserialize(json_encode('+16502530000'), PhoneNumber::class)
        );
    }
}
