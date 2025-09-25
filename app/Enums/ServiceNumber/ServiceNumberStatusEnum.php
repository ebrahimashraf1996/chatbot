<?php

namespace App\Enums\ServiceNumber;


enum ServiceNumberStatusEnum: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => __('Active'),
            self::Inactive => __('Inactive'),
        };
    }
}
