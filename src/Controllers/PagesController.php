<?php

namespace Nksoft\Articles\Controllers;

use Arr;
use Illuminate\Http\Request;
use Nksoft\Articles\Models\Menus;
use Nksoft\Articles\Models\Pages as CurrentModel;
use Nksoft\Master\Controllers\WebController;

class PagesController extends WebController
{
    private $formData = ['id', 'name', 'page_template', 'is_active', 'order_by', 'slug', 'description', 'meta_description'];

    protected $module = 'pages';

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
            $results = CurrentModel::select($select)->with(['histories'])->paginate();
            $listDelete = $this->getHistories($this->module)->pluck('parent_id');
            $response = [
                'rows' => $results,
                'columns' => $columns,
                'module' => $this->module,
                'listDelete' => CurrentModel::whereIn('id', $listDelete)->get(),
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
            ];
            return $this->responseSuccess($response);
        } catch (\Execption $e) {
            return $this->responseError($e);
        }
    }

    public function pageTemplate()
    {
        $pages = [];
        foreach (config('nksoft.pageTemplate') as $v => $k) {
            $pages[] = ['id' => $k['id'], 'name' => trans('nksoft::common.layout.' . $k['name'])];
        }
        return $pages;
    }

    private function formElement($result = null)
    {
        return [
            [
                'key' => 'general',
                'label' => trans('nksoft::common.General'),
                'element' => [
                    ['key' => 'is_active', 'label' => trans('nksoft::common.Status'), 'data' => $this->status(), 'type' => 'select'],
                    ['key' => 'page_template', 'label' => trans('nksoft::common.Layout Page'), 'data' => $this->pageTemplate(), 'type' => 'select'],
                    ['key' => 'meta_description', 'label' => trans('nksoft::common.Meta Description'), 'data' => null, 'type' => 'textarea'],
                ],
                'active' => true,
            ],
            [
                'key' => 'inputForm',
                'label' => trans('nksoft::common.Content'),
                'element' => [
                    ['key' => 'name', 'label' => trans('nksoft::common.Name'), 'data' => null, 'class' => 'required', 'type' => 'text'],
                    ['key' => 'description', 'label' => trans('nksoft::common.Description'), 'data' => null, 'type' => 'editor'],
                    ['key' => 'order_by', 'label' => trans('nksoft::common.Order By'), 'data' => null, 'type' => 'number'],
                    ['key' => 'slug', 'label' => trans('nksoft::common.Slug'), 'data' => null, 'type' => 'text'],
                    ['key' => 'images', 'label' => trans('nksoft::common.Images'), 'data' => null, 'type' => 'image'],
                ],
            ],
        ];
    }

    private function rules()
    {
        $rules = [
            'name' => 'required',
            'images[]' => 'image',
        ];

        return $rules;
    }

    private function message()
    {
        return [
            'name.required' => __('nksoft::message.Field is require!', ['Field' => trans('nksoft::common.Name')]),
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
            $result = CurrentModel::create($data);
            $this->setUrlRedirects($result);
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
        try {
            $result = CurrentModel::select(['description', 'name', 'page_template', 'id'])->with(['images', 'banners'])->find($id);
            if (!$result) {
                return $this->responseError('404');
            }
            $image = $result->images()->first();
            $im = $image ? 'storage/' . $image->image : 'wine/images/share/logo.svg';
            $response = [
                'result' => $result,
                'banner' => $result->images->first(),
                'layout' => $result->page_template,
                'template' => $this->module,
                'menus' => Menus::getListMenuView(),
                'breadcrumb' => [
                    ['link' => '/', 'label' => \trans('nksoft::common.Home')],
                    ['active' => true, 'link' => '#', 'label' => $result->name],
                ],
                'seo' => [
                    'title' => $result->name,
                    'ogDescription' => $result->meta_description,
                    'ogUrl' => url($result->slug),
                    'ogImage' => url($im),
                    'ogSiteName' => $result->name,
                ],
            ];
            return $this->responseViewSuccess($response);
        } catch (\Exception $e) {
            return $this->responseError($e->getMessage());
        }
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
            foreach ($data as $k => $v) {
                $result->$k = $v;
            }

            $result->save();
            $this->setUrlRedirects($result);
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
