<?php
namespace Nksoft\Articles\Models;

use Nksoft\Master\Models\NksoftModel;

class Articles extends NksoftModel
{
    protected $table = 'articles';
    protected $fillable = ['id', 'name', 'is_active', 'order_by', 'slug', 'description', 'short_content', 'categories_id', 'meta_description'];
}
