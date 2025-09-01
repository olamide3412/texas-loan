<?php

namespace App\Enums;

enum VerificationStatusEnums  : string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';

}
