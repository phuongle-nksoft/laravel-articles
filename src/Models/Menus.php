<?php
namespace Nksoft\Articles\Models;

use Nksoft\Master\Models\NksoftModel;

class Menus extends NksoftModel
{
    const FIELDS = ['id', 'name', 'parent_id', 'is_active', 'order_by', 'slug', 'url_to', 'type', 'icon', 'layout', 'position', 'meta_description', 'canonical_link'];
    protected $table = 'menus';
    protected $fillable = self::FIELDS;

    /**
     * Get list category to product
     */
    public static function GetListMenu($where, $result, $listDelete = [])
    {
        $parentId = $result->parent_id ?? 0;
        $id = $result->id ?? 0;
        $data = array();
        if ($listDelete && count($listDelete) > 0) {
            $fs = self::whereIn('id', $listDelete)->get();
        } else {
            $fs = self::where($where)->where('id', '<>', $id)->with(['histories'])->orderBy('order_by')->get();
        }
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
                    'children' => self::GetListMenu(['parent_id' => $item->id], $result),
                    'slug' => $item->slug,
                    'layout' => $item->layout,
                    'histories' => $item->histories,
                );
            }
        }
        return $data;
    }

    public static function getListMenuView($where = ['parent_id' => 0])
    {
        $fs = self::where($where)->where(['is_active' => 1])->orderBy('order_by')->get();
        $data = array();
        if ($fs) {
            foreach ($fs as $item) {
                $data[] = array(
                    'text' => $item->name,
                    'id' => $item->id,
                    'parent_id' => $item->parent_id,
                    'position' => strval($item->position),
                    'children' => self::getListMenuView(['parent_id' => $item->id]),
                    'slug' => $item->slug,
                    'icon' => $item->icon,
                    'layout' => $item->layout,
                    'type' => $item->type,
                );
            }
        }
        return $data;
    }
}
