<?php

namespace Nksoft\Articles\Models;

use Nksoft\Master\Models\NksoftModel;

class Pages extends NksoftModel
{
    protected $table = 'pages';
    protected $fillable = ['id', 'name', 'is_active', 'order_by', 'slug', 'description', 'meta_description'];
}
