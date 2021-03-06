<?php

namespace Nksoft\Articles\Controllers;

use Arr;
use Illuminate\Http\Request;
use Nksoft\Articles\Models\ArticleCategories;
use Nksoft\Articles\Models\Menus as CurrentModel;
use Nksoft\Articles\Models\Pages;
use Nksoft\Master\Controllers\WebController;
use Nksoft\Products\Models\Brands;
use Nksoft\Products\Models\Categories;
use Nksoft\Products\Models\Regions;
use Nksoft\Products\Models\Vintages;

class MenusController extends WebController
{
    private $formData = CurrentModel::FIELDS;

    protected $module = 'menus';

    protected $model = CurrentModel::class;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $columns = [
                ['key' => 'id', 'label' => 'Id', 'type' => 'hidden'],
                ['key' => 'name', 'label' => trans('nksoft::common.Name')],
                ['key' => 'is_active', 'label' => trans('nksoft::common.Status'), 'data' => $this->status(), 'type' => 'select'],
            ];
            $select = Arr::pluck($columns, 'key');
            $results = CurrentModel::GetListMenu(['parent_id' => 0], null);
            $listDelete = $this->getHistories($this->module)->pluck('parent_id');
            $response = [
                'rows' => $results,
                'columns' => $columns,
                'module' => $this->module,
                'listDelete' => $listDelete && count($listDelete) > 0 ? CurrentModel::GetListMenu(['parent_id' => 0], null, $listDelete) : null,
                'layout' => 'menus',
            ];
            return $this->responseSuccess($response);
        } catch (\Execption $e) {
            return $this->responseError($e);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            \array_push($this->formData, 'images');
            \array_push($this->formData, 'none_slug');
            $response = [
                'formElement' => $this->formElement(),
                'result' => null,
                'formData' => $this->formData,
                'module' => $this->module,
                'template' => 'menus',
            ];
            return $this->responseSuccess($response);
        } catch (\Execption $e) {
            return $this->responseError($e);
        }
    }

    private function getPosition($result)
    {
        $idSelected = $result ? json_decode($result->position) : [];
        if (!is_array($idSelected)) {
            $idSelected = [$idSelected];
        }

        $data = array();
        foreach ($this->position() as $item) {
            $selected = array(
                'opened' => false,
                'selected' => in_array($item['id'], $idSelected) ? true : false,
            );
            $data[] = array(
                'name' => $item['name'],
                'icon' => 'fas fa-folder',
                'id' => $item['id'],
                'state' => $selected,
                'children' => null,
                'slug' => '',
            );
        }
        return $data;
    }

    private function formElement($result = null)
    {
        $categories = [
            [
                'name' => trans('nksoft::common.article categories'),
                'id' => 0,
                'icon' => 'fas fa-folder',
                'type' => 'article-categories',
                'state' => [
                    'opened' => true,
                    'selected' => $result && $result->url_to == 0 && $result->type == 'article-categories',
                ],
                'children' => ArticleCategories::GetListWithParentByMenu(array('parent_id' => 0), $result, 'article-categories'),
            ],
            [
                'name' => trans('nksoft::common.pages'),
                'id' => 0,
                'icon' => 'fas fa-folder',
                'type' => 'pages',
                'state' => [
                    'opened' => true,
                    'selected' => $result && $result->url_to == 0 && $result->type == 'pages',
                ],
                'children' => Pages::GetListByMenu($result, 'pages'),
            ],
            [
                'name' => trans('nksoft::common.categories'),
                'id' => 0,
                'icon' => 'fas fa-folder',
                'type' => 'categories',
                'state' => [
                    'opened' => true,
                    'selected' => $result && $result->url_to == 0 && $result->type == 'categories',
                ],
                'children' => Categories::GetListWithParentByMenu(array('parent_id' => 0), $result, 'categories'),
            ],
            [
                'name' => trans('nksoft::common.brands'),
                'id' => 0,
                'icon' => 'fas fa-folder',
                'type' => 'brands',
                'state' => [
                    'opened' => true,
                    'selected' => $result && $result->url_to == 0 && $result->type == 'brands',
                ],
                'children' => Brands::GetListByMenu($result, 'brands'),
            ],
            [
                'name' => trans('nksoft::common.vintages'),
                'id' => 0,
                'icon' => 'fas fa-folder',
                'type' => 'vintages',
                'state' => [
                    'opened' => true,
                    'selected' => $result && $result->url_to == 0 && $result->type == 'vintages',
                ],
                'children' => Vintages::GetListByMenu($result, 'vintages'),
            ],
            [
                'name' => trans('nksoft::common.regions'),
                'id' => 0,
                'icon' => 'fas fa-folder',
                'type' => 'regions',
                'state' => [
                    'opened' => true,
                    'selected' => $result && $result->url_to == 0 && $result->type == 'regions',
                ],
                'children' => Regions::GetListWithParentByMenu(array('parent_id' => 0), $result, 'regions'),
            ],
            [
                'name' => trans('nksoft::common.products'),
                'id' => 1,
                'icon' => 'fas fa-folder',
                'type' => 'products',
                'state' => [
                    'opened' => true,
                    'selected' => $result && $result->url_to == 1 && $result->type == 'products',
                ],
                'children' => [],
            ],
        ];
        $parent = [
            [
                'name' => trans('nksoft::common.root'),
                'id' => 0,
                'icon' => 'fas fa-folder',
                'state' => [
                    'opened' => true,
                    'selected' => $result && $result->parent_id == 0,
                ],
                'children' => CurrentModel::GetListMenu(array('parent_id' => 0), $result),
            ],
        ];
        return [
            [
                'key' => 'inputForm',
                'label' => trans('nksoft::common.Content'),
                'element' => [
                    ['key' => 'url_to', 'label' => trans('nksoft::common.Url To'), 'data' => $categories, 'type' => 'select'],
                    ['key' => 'parent_id', 'label' => trans('nksoft::common.root'), 'data' => $parent, 'type' => 'select'],
                    ['key' => 'position', 'label' => trans('nksoft::common.Position.Title'), 'data' => $this->getPosition($result), 'multiple' => true, 'type' => 'tree'],
                    ['key' => 'is_active', 'label' => trans('nksoft::common.Status'), 'data' => $this->status(), 'type' => 'select'],
                    ['key' => 'name', 'label' => trans('nksoft::common.Name'), 'data' => null, 'class' => 'required', 'type' => 'text'],
                    ['key' => 'order_by', 'label' => trans('nksoft::common.Order By'), 'data' => null, 'type' => 'number'],
                    ['key' => 'icon', 'label' => trans('nksoft::common.Icon'), 'data' => null, 'type' => 'text'],
                    ['key' => 'layout', 'label' => trans('nksoft::articles.Menu Layout'), 'data' => config('nksoft.menuLayout'), 'type' => 'select'],
                    ['key' => 'slug', 'label' => trans('nksoft::common.Slug'), 'data' => null, 'type' => 'text'],
                    ['key' => 'none_slug', 'label' => trans('nksoft::common.None Slug'), 'data' => null, 'type' => 'checkbox'],
                ],
                'active' => true,
            ],
            [
                'key' => 'SEO',
                'label' => 'SEO',
                'element' => [
                    ['key' => 'canonical_link', 'label' => 'Canonical Link', 'data' => null, 'type' => 'text'],
                    ['key' => 'meta_title', 'label' => 'Title', 'data' => null, 'type' => 'text'],
                    ['key' => 'meta_description', 'label' => trans('nksoft::common.Meta Description'), 'data' => null, 'type' => 'textarea'],
                ],
            ],
        ];
    }
    public function position()
    {
        $status = [];
        foreach (config('nksoft.position') as $v => $k) {
            $status[] = ['id' => $k['id'], 'name' => trans('nksoft::common.Position.' . $k['name'])];
        }
        return $status;
    }
    private function rules()
    {
        $rules = [
            'name' => 'required',
            'url_to' => 'required',
            'images[]' => 'image',
        ];

        return $rules;
    }

    private function message()
    {
        return [
            'name.required' => __('nksoft::message.Field is require!', ['Field' => trans('nksoft::common.Name')]),
            'url_to.required' => __('nksoft::message.Field is require!', ['Field' => trans('nksoft::common.Url To')]),
        ];
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator($request->all(), $this->rules(), $this->message());
        if ($validator->fails()) {
            return \response()->json(['status' => 'error', 'message' => $validator->errors()]);
        }
        try {
            $data = [];
            foreach ($this->formData as $item) {
                if (!in_array($item, $this->excludeCol)) {
                    $data[$item] = $request->get($item);
                }
            }
            if ($request->get('duplicate')) {
                $data['slug'] = null;
            }
            $data['slug'] = $this->getSlug($data);
            if ($request->get('none_slug')) {
                $data['slug'] = null;
            }

            if (!$data['parent_id']) {
                $data['parent_id'] = 0;
            }
            // $data['position'] = \implode(',', json_decode($data['position']));
            $result = CurrentModel::create($data);
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $this->setMedia($images, $result->id, $this->module);
            }
            if ($request->hasFile('banner')) {
                $images = $request->file('banner');
                $this->setMedia($images, $result->id, $this->module, true);
            }
            $response = [
                'result' => $result,
            ];
            return $this->responseSuccess($response);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('master::layout');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $result = CurrentModel::select($this->formData)->with(['images'])->find($id);
            // $result->position = \json_encode(\explode(',', $result->position));
            \array_push($this->formData, 'images');
            \array_push($this->formData, 'none_slug');
            $result->none_slug = !$result->slug ? 1 : 0;
            $response = [
                'formElement' => $this->formElement($result),
                'result' => $result,
                'formData' => $this->formData,
                'module' => $this->module,
            ];
            return $this->responseSuccess($response);
        } catch (\Execption $e) {
            return $this->responseError($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $result = CurrentModel::find($id);
        if ($result == null) {
            return $this->responseError();
        }
        $validator = Validator($request->all(), $this->rules($id), $this->message());
        if ($validator->fails()) {
            return \response()->json(['status' => 'error', 'message' => $validator->errors()]);
        }
        try {
            $data = [];
            foreach ($this->formData as $item) {
                if (!in_array($item, $this->excludeCol)) {
                    $data[$item] = $request->get($item);
                }
            }
            if (!$data['parent_id']) {
                $data['parent_id'] = 0;
            }
            $data['slug'] = $this->getSlug($data);
            if ($request->get('none_slug')) {
                $data['slug'] = null;
            }
            // $data['position'] = \implode(',', json_decode($data['position']));
            foreach ($data as $k => $v) {
                $result->$k = $v;
            }

            $result->save();
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $this->setMedia($images, $result->id, $this->module);
            }
            if ($request->hasFile('banner')) {
                $images = $request->file('banner');
                $this->setMedia($images, $result->id, $this->module, true);
            }
            $response = [
                'result' => $result,
            ];
            return $this->responseSuccess($response);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }
}
