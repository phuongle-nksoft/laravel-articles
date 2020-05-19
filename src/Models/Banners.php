<?php

namespace Nksoft\Articles\Models;

use Nksoft\Master\Models\NksoftModel;

class Banners extends NksoftModel
{
    const FIELDS = ['id', 'name', 'is_active', 'pages_id', 'order_by', 'description', 'slug'];
    protected $table = 'banners';
    protected $fillable = self::FIELDS;
}
