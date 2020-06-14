<?php
namespace Nksoft\Articles\Models;

use Nksoft\Master\Models\NksoftModel;

class Articles extends NksoftModel
{
    const FIELDS = ['id', 'name', 'is_active', 'order_by', 'slug', 'description', 'short_content', 'categories_id', 'meta_title', 'meta_description'];
    protected $table = 'articles';
    protected $fillable = self::FIELDS;

    public function category()
    {
        return $this->belongsTo(ArticleCategories::class, 'categories_id')->select(['id', 'name', 'slug']);
    }
}
