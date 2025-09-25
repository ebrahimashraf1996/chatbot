<?php

namespace App\Enums\Conversation;


enum ConversationStatusEnum: string
{
    case Active = 'active';
    case Finished = 'finished';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => __('Active'),
            self::Finished => __('Finished'),
        };
    }
}
