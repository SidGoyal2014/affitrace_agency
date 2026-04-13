<?php

namespace App\Enums;

enum WeeklyPayoutStatus: string
{
    case Pending = 'PENDING';
    case Paid = 'PAID';
    case Rejected = 'REJECTED';
}