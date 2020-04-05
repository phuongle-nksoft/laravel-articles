<?php
namespace Nksoft\Articles\Models;

use Nksoft\Master\Models\NksoftModel;

class Blocks extends NksoftModel
{
    protected $table = 'blocks';
    protected $fillable = ['id', 'name', 'is_active', 'order_by', 'identify', 'description', 'slug', 'meta_description'];
}
