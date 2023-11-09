<?php

namespace App\Models;

enum UpdateRequestStatus: string
{

    case WAITING_AUTHENTICATION = 'waiting_authentication';
    case UNAUTHORIZED = 'unauthorized';
    case AUTHORIZED = 'authorized';
    case DOWNLOADED = 'downloaded';

    public static function all(): array
    {
        return [
            self::WAITING_AUTHENTICATION,
            self::UNAUTHORIZED,
            self::AUTHORIZED,
            self::DOWNLOADED,
        ];
    }

}
