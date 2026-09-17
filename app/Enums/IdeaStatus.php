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

    public static function values(): array
    {
        // return array_column(IdeaStatus::cases(), 'value');
        return array_map(fn (IdeaStatus $status) => $status->value, self::cases());
    }

    public static function has(string $value): bool
    {
        return in_array(strtolower(trim($value)), self::values(), true);
    }

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

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::IN_PROGRESS => 'blue',
            self::ACTIVE => 'green',
            self::COMPLETED => 'teal',
            self::INCOMPLETED => 'orange',
            self::DRAFT => 'gray',
            self::PAUSED => 'amber',
            self::CANCELLED => 'red',
            self::ARCHIVED => 'purple',
        };
    }
}
