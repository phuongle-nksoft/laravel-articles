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
    private $formData = ['id', 'name', 'parent_id', 'is_active', 'order_by', 'slug', 'url_to', 'position', 'type', 'meta_description'];

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
                ['key' => 'id', 'label' => 'Id'],
                ['key' => 'name', 'label' => trans('nksoft::common.Name')],
                ['key' => 'is_active', 'label' => trans('nksoft::common.Status'), 'data' => $this->status()],
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
                'text' => $item['name'],
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
                'text' => trans('nksoft::common.article categories'),
                'id' => 0,
                'icon' => 'fas fa-folder',
                'type' => 'article-categories',
                'state' => [
                    'opened' => true,
                    'selected' => $result && $result->parent_id == 0,
                ],
                'children' => ArticleCategories::GetListWithParentByMenu(array('parent_id' => 0), $result, 'article-categories'),
            ],
            [
                'text' => trans('nksoft::common.pages'),
                'id' => 0,
                'icon' => 'fas fa-folder',
                'type' => 'pages',
                'state' => [
                    'opened' => true,
                    'selected' => $result && $result->parent_id == 0,
                ],
                'children' => Pages::GetListByMenu($result, 'pages'),
            ],
            [
                'text' => trans('nksoft::common.categories'),
                'id' => 0,
                'icon' => 'fas fa-folder',
                'type' => 'categories',
                'state' => [
                    'opened' => true,
                    'selected' => $result && $result->parent_id == 0,
                ],
                'children' => Categories::GetListWithParentByMenu(array('parent_id' => 0), $result, 'categories'),
            ],
            [
                'text' => trans('nksoft::common.brands'),
                'id' => 0,
                'icon' => 'fas fa-folder',
                'type' => 'brands',
                'state' => [
                    'opened' => true,
                    'selected' => $result && $result->parent_id == 0,
                ],
                'children' => Brands::GetListByMenu($result, 'brands'),
            ],
            [
                'text' => trans('nksoft::common.vintages'),
                'id' => 0,
                'icon' => 'fas fa-folder',
                'type' => 'vintages',
                'state' => [
                    'opened' => true,
                    'selected' => $result && $result->parent_id == 0,
                ],
                'children' => Vintages::GetListByMenu($result, 'vintages'),
            ],
            [
                'text' => trans('nksoft::common.regions'),
                'id' => 0,
                'icon' => 'fas fa-folder',
                'type' => 'regions',
                'state' => [
                    'opened' => true,
                    'selected' => $result && $result->parent_id == 0,
                ],
                'children' => Regions::GetListWithParentByMenu(array('parent_id' => 0), $result, 'regions'),
            ],
        ];
        $parent = [
            [
                'text' => trans('nksoft::common.root'),
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
                'key' => 'general',
                'label' => trans('nksoft::common.General'),
                'element' => [
                    ['key' => 'url_to', 'label' => trans('nksoft::common.Url To'), 'data' => $categories, 'type' => 'tree'],
                ],
                'active' => true,
            ],
            [
                'key' => 'inputForm',
                'label' => trans('nksoft::common.Content'),
                'element' => [
                    ['key' => 'parent_id', 'label' => trans('nksoft::common.root'), 'data' => $parent, 'type' => 'tree'],
                    ['key' => 'position', 'label' => trans('nksoft::common.Position.Title'), 'data' => $this->getPosition($result), 'multiple' => true, 'type' => 'tree'],
                    ['key' => 'is_active', 'label' => trans('nksoft::common.Status'), 'data' => $this->status(), 'type' => 'select'],
                    ['key' => 'name', 'label' => trans('nksoft::common.Name'), 'data' => null, 'class' => 'required', 'type' => 'text'],
                    ['key' => 'order_by', 'label' => trans('nksoft::common.Order By'), 'data' => null, 'type' => 'number'],
                    ['key' => 'slug', 'label' => trans('nksoft::common.Slug'), 'data' => null, 'type' => 'text'],
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
            $data['slug'] = $this->getSlug($data);
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
            $data['slug'] = $this->getSlug($data);
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
