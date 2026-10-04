<?php

namespace App\Enums\Report;

enum ReportReason: string
{
    case Spam = 'spam';
    case Fraud = 'fraud';
    case Inappropriate = 'inappropriate';
    case IncorrectInfo = 'incorrect_info';
    case Other = 'other';
}
