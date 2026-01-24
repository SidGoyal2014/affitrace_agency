<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft = 'DRAFT';
    case Sent = 'SENT';
    case Paid = 'PAID';
    case Deleted = 'DELETED';
}
