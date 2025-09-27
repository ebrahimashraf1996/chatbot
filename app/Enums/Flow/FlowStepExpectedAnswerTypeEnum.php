<?php

namespace App\Enums\Flow;


enum FlowStepExpectedAnswerTypeEnum: string
{
    case Text = 'text';
    case Number = 'number';
    case Choice = 'choice';
    case End = 'end';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Text => __('Text'),
            self::Number => __('Number'),
            self::Choice => __('Choice'),
            self::End => __('End'),
        };
    }
}
