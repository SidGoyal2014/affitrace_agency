<?php

namespace App\Enums;

enum ConversionStatus: string
{
    case Converted = 'converted';
    case Pending = 'pending';
    case Rejected = 'rejected'; // Assuming rejected exists or will exist
}
