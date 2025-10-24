<?php

namespace App\Enum;

enum ResponseStatus: string
{
    case Success = 'success';
    case Error = 'error';
}