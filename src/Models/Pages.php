<?php

namespace Nksoft\Articles\Models;

use Nksoft\Master\Models\NksoftModel;

class Pages extends NksoftModel
{
    const FIELDS = ['id', 'name', 'is_active', 'order_by', 'slug', 'description', 'meta_title', 'meta_description', 'canonical_link'];
    protected $table = 'pages';
    protected $fillable = self::FIELDS;

    public function banners()
    {
        return $this->hasMany(Banners::class, 'pages_id')->where(['is_active' => 1])->with('images');
    }
}
