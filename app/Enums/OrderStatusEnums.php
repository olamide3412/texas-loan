<?php

namespace App\Enums;

enum OrderStatusEnums  : string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Partial = 'partial';
    case Completed = 'completed';
}
