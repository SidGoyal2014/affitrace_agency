<?php

namespace App\Enums;

enum OfferStatus: string
{
    case Active = 'active';
    case Approved = 'approve'; // AdminOfferService uses 'approve'
    case Pending = 'pending';
    case Ended = 'ended';
    // case ApprovedCapital = 'Approved'; // Seen in AdvertiserOfferService, keeping commented for now unless needed
}
