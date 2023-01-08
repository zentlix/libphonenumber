<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Tests\App\Request;

use Spiral\Filters\Attribute\Input\Post;
use Spiral\PhoneNumber\Validator\Constraints\PhoneNumber;
use Spiral\Validation\Symfony\AttributesFilter;

final class PhoneRequest extends AttributesFilter
{
    #[Post]
    #[PhoneNumber]
    public string $phone;

    #[Post(key: 'phone_with_custom_error')]
    #[PhoneNumber(message: 'foo')]
    public string $phoneWithCustomError;
}
