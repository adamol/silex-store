<?php

namespace App\Products;

use App\Repository as BaseRepository;

class Repository extends BaseRepository
{
    const TABLE_NAME = 'products';

    const TABLE_COLUMNS = [
        'display_name',
        'product_type',
        'price'
    ];

    public function table()
    {
        return self::TABLE_NAME;
    }

    public function columns()
    {
        return self::TABLE_COLUMNS;
    }
}
