<?php

namespace Nksoft\Articles\database\seeds;

use Illuminate\Database\Seeder;
use Nksoft\Master\Models\Navigations;

class NavigationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $article = [
            [
                'title' => 'Banners',
                'link' => 'banners',
                'icon' => 'nav-icon far fa-images',
                'is_active' => true,
                'order_by' => 1,
                'roles_id' => json_encode([1, 2]),
            ],
            [
                'title' => 'Menus',
                'link' => 'menus',
                'icon' => 'nav-icon fas fa-ellipsis-v',
                'is_active' => true,
                'order_by' => 1,
                'roles_id' => json_encode([1, 2]),
            ],
            [
                'title' => 'Pages',
                'link' => 'pages',
                'icon' => 'nav-icon fas fa-swatchbook',
                'is_active' => true,
                'order_by' => 2,
                'roles_id' => json_encode([1, 2]),
            ],
            [
                'title' => 'Blocks',
                'link' => 'blocks',
                'icon' => 'nav-icon fas fa-cubes',
                'is_active' => true,
                'order_by' => 3,
                'roles_id' => json_encode([1, 2]),
            ],
            [
                'title' => 'Article Categories',
                'link' => 'article-categories',
                'icon' => 'nav-icon fas fa-th',
                'is_active' => true,
                'order_by' => 4,
                'roles_id' => json_encode([1, 2]),
            ],
            [
                'title' => 'Articles',
                'link' => 'articles',
                'icon' => 'nav-icon far fa-newspaper',
                'is_active' => true,
                'order_by' => 5,
                'roles_id' => json_encode([1, 2]),
            ],
        ];
        $items = [
            [
                'title' => 'Articles',
                'link' => '#',
                'icon' => '',
                'is_active' => true,
                'order_by' => 0,
                'roles_id' => json_encode([1, 2]),
                'child' => serialize($article),
            ],
        ];
        Navigations::saveItem($items);
    }
}
