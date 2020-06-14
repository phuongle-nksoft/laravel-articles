<?php

namespace Nksoft\Articles\Models;

use Nksoft\Master\Models\NksoftModel;

class ArticleCategories extends NksoftModel
{
    const FIELDS = ['id', 'name', 'is_active', 'order_by', 'parent_id', 'slug', 'description', 'page_template', 'meta_title', 'meta_description'];
    protected $table = 'article_categories';
    protected $fillable = self::FIELDS;

    public function images()
    {
        return $this->hasMany('\Nksoft\Master\Models\FilesUpload', 'parent_id')->where(['type' => 'article-categories']);
    }

    public function articles()
    {
        return $this->hasMany(Articles::class, 'categories_id')->select(['slug', 'short_content', 'id', 'name', 'is_active', 'categories_id', 'description'])->where(['is_active' => 1])->with(['images']);
    }

    public function childrens()
    {
        return $this->hasMany(ArticleCategories::class, 'parent_id')->select(['slug', 'id', 'name', 'is_active', 'parent_id', 'description'])->where(['is_active' => 1])->with(['images', 'articles', 'childrens']);
    }

    /**
     * Get list category with recursive
     */
    public static function GetListCategories($where, $result)
    {
        $parentId = $result->parent_id ?? 0;
        $id = $result->id ?? 0;
        $data = array();
        $fs = self::where($where)->where('id', '<>', $id)->orderBy('order_by')->get();
        if ($fs) {
            foreach ($fs as $item) {
                $selected = array(
                    'opened' => false,
                    'selected' => $item->id === $parentId ? true : false,
                );
                $data[] = array(
                    'name' => $item->name,
                    'icon' => 'fas fa-folder',
                    'id' => $item->id,
                    'state' => $selected,
                    'children' => self::GetListCategories(['parent_id' => $item->id], $result),
                    'slug' => $item->slug,
                );
            }
        }
        return $data;
    }

    /**
     * Get list category to product
     */
    public static function GetListByArticle($where, $result)
    {
        $type = 'article-categories';
        $parentId = $result->categories_id ?? 0;
        $data = array();
        $fs = self::where($where)->orderBy('order_by')->get();
        if ($fs) {
            foreach ($fs as $item) {
                $selected = array(
                    'opened' => false,
                    'selected' => $item->id === $parentId ? true : false,
                );
                $data[] = array(
                    'name' => $item->name,
                    'icon' => 'fas fa-folder',
                    'id' => $item->id,
                    'state' => $selected,
                    'children' => self::GetListByArticle(['parent_id' => $item->id], $result),
                    'slug' => $item->slug,
                );
            }
        }
        return $data;
    }

}
