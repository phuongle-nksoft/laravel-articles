<?php
namespace Nksoft\Articles\Models;

use Nksoft\Master\Models\NksoftModel;

class Menus extends NksoftModel
{
    protected $table = 'menus';
    protected $fillable = ['id', 'name', 'parent_id', 'is_active', 'order_by', 'slug', 'url_to', 'meta_description'];

    /**
     * Get list category to product
     */
    public static function GetListMenu($where, $result)
    {
        $parentId = $result->parent_id ?? 0;
        $data = array();
        $fs = self::where($where)->orderBy('order_by')->get();
        if ($fs) {
            foreach ($fs as $item) {
                $selected = array(
                    'opened' => false,
                    'selected' => $item->id === $parentId ? true : false,
                );
                $data[] = array(
                    'text' => $item->name,
                    'icon' => 'fas fa-folder',
                    'id' => $item->id,
                    'state' => $selected,
                    'children' => self::GetListMenu(['parent_id' => $item->id], $result),
                    'slug' => $item->slug,
                );
            }
        }
        return $data;
    }
}
