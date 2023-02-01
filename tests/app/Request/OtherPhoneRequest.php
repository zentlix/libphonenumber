<?php

declare(strict_types=1);

namespace Spiral\PhoneNumber\Tests\App\Request;

use Spiral\Filters\Attribute\Input\Post;
use Spiral\Filters\Model\Filter;
use Spiral\Filters\Model\FilterDefinitionInterface;
use Spiral\Filters\Model\HasFilterDefinition;
use Spiral\Validator\FilterDefinition;

final class OtherPhoneRequest extends Filter implements HasFilterDefinition
{
    #[Post]
    public string $phone;

    #[Post(key: 'phone_with_custom_error')]
    public string $phoneWithCustomError;

    public function filterDefinition(): FilterDefinitionInterface
    {
        return new FilterDefinition([
            'phone' => ['phone'],
            'phoneWithCustomError' => [
                ['phone', 'error' => 'Custom error.']
            ]
        ]);
    }
}
