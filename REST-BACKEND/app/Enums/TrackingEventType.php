<?php

namespace App\Enums;

enum TrackingEventType: string
{
    case Click = 'click';
    case Conversion = 'conversion';
    case Impression = 'impression';
}
