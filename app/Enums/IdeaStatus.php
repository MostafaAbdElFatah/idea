<?php

declare(strict_types=1);

namespace App\Enums;

enum IdeaStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case INCOMPLETED = 'incompleted';
    case DRAFT = 'draft';
    case PAUSED = 'paused';
    case CANCELLED = 'cancelled';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In progress',
            self::ACTIVE => 'Active',
            self::COMPLETED => 'Completed',
            self::INCOMPLETED => 'In Completed',
            self::DRAFT => 'Draft',
            self::PAUSED => 'Paused',
            self::CANCELLED => 'Cancelled',
            self::ARCHIVED => 'Archived'
        };
    }
}
