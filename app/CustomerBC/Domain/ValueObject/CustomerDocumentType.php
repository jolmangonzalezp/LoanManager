<?php

declare(strict_types=1);

namespace App\CustomerBC\Domain\ValueObject;

enum CustomerDocumentType: string
{
    case PERSON_PHOTO = 'person_photo';
    case IDENTITY_DOCUMENT = 'identity_document';
    case HOUSE_PHOTO = 'house_photo';

    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
