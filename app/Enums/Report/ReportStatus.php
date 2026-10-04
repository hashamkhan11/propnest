<?php

namespace App\Enums\Report;

enum ReportStatus: string
{
    case Pending = 'pending';
    case Dismissed = 'dismissed';
    case ActionTaken = 'action_taken';
}
