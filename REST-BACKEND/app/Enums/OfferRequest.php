<?php

namespace App\Enums;

enum OfferRequest: string
{
    case Accepted = 'Accepted';
    case Pending = 'Pending';
    case Rejected = 'Rejected';
    case Unassigned = 'Unassigned';
}
