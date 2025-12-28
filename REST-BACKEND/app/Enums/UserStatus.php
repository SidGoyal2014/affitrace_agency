<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active = 'Active';
    case Pending = 'Pending';
    case Blocked = 'Blocked';

    // Some parts of the code use lowercase, so we might need helper methods or careful usage
    // But the primary storage seems to be capitalized based on IntegrationService logic
}
