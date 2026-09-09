<?php

declare(strict_types=1);

use App\Enums\IdeaStatus;

dataset('idea statuses', [
    'pending' => [IdeaStatus::PENDING, 'pending', 'Pending'],
    'in progress' => [IdeaStatus::IN_PROGRESS, 'in_progress', 'In progress'],
    'active' => [IdeaStatus::ACTIVE, 'active', 'Active'],
    'completed' => [IdeaStatus::COMPLETED, 'completed', 'Completed'],
    'incompleted' => [IdeaStatus::INCOMPLETED, 'incompleted', 'In Completed'],
    'draft' => [IdeaStatus::DRAFT, 'draft', 'Draft'],
    'paused' => [IdeaStatus::PAUSED, 'paused', 'Paused'],
    'cancelled' => [IdeaStatus::CANCELLED, 'cancelled', 'Cancelled'],
    'archived' => [IdeaStatus::ARCHIVED, 'archived', 'Archived'],
]);
