<?php

namespace Nksoft\Articles\Controllers;

use Arr;
use Illuminate\Http\Request;
use Nksoft\Articles\Models\ArticleCategories;
use Nksoft\Articles\Models\Articles as CurrentModel;
use Nksoft\Articles\Models\Menus;
use Nksoft\Master\Controllers\WebController;

class ArticlesController extends WebController
{
    private $formData = CurrentModel::FIELDS;

    protected $module = 'articles';
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
                ['key' => 'order_by', 'label' => 'STT', 'widthCol' => 80],
                ['key' => 'id', 'label' => 'Id', 'type' => 'hidden'],
                ['key' => 'name', 'label' => trans('nksoft::common.Name')],
                ['key' => 'categories_id', 'label' => trans('nksoft::common.article categories'), 'data' => ArticleCategories::GetListByArticle(array('parent_id' => 0)), 'type' => 'select', 'relationship' => 'category'],
                ['key' => 'is_active', 'label' => trans('nksoft::common.Status'), 'data' => $this->status(), 'type' => 'select'],
                ['key' => 'created_at', 'label' => 'Ngày Đăng', 'formatter' => 'date'],
            ];
            $select = Arr::pluck($columns, 'key');
            $results = CurrentModel::select($select)->with(['histories', 'category'])->orderBy('created_at', 'desc')->get();
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
            \array_push($this->formData, 'banner');
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

    private function formElement($result = null)
    {
        $categories = ArticleCategories::GetListByArticle(array('parent_id' => 0), $result);
        return [
            [
                'key' => 'general',
                'label' => trans('nksoft::common.General'),
                'element' => [
                    ['key' => 'is_active', 'label' => trans('nksoft::common.Status'), 'data' => $this->status(), 'type' => 'select'],
                    ['key' => 'categories_id', 'label' => trans('nksoft::common.article categories'), 'data' => $categories, 'type' => 'tree'],
                    ['key' => 'name', 'label' => trans('nksoft::common.Name'), 'data' => null, 'class' => 'required', 'type' => 'text'],
                    ['key' => 'short_content', 'label' => trans('nksoft::common.Short Content'), 'data' => null, 'type' => 'editor'],
                    ['key' => 'description', 'label' => trans('nksoft::common.Description'), 'data' => null, 'type' => 'editor'],
                    ['key' => 'order_by', 'label' => trans('nksoft::common.Order By'), 'data' => null, 'type' => 'number'],
                    ['key' => 'slug', 'label' => trans('nksoft::common.Slug'), 'data' => null, 'type' => 'text'],
                    ['key' => 'banner', 'label' => trans('nksoft::common.Banner'), 'data' => null, 'type' => 'image'],
                    ['key' => 'images', 'label' => trans('nksoft::common.Images'), 'data' => null, 'type' => 'image'],
                ],
                'active' => true,
            ],
            [
                'key' => 'inputForm',
                'label' => 'SEO',
                'element' => [
                    ['key' => 'canonical_link', 'label' => 'Canonical Link', 'data' => null, 'type' => 'text'],
                    ['key' => 'meta_title', 'label' => 'Title', 'data' => null, 'type' => 'text'],
                    ['key' => 'meta_description', 'label' => trans('nksoft::common.Meta Description'), 'data' => null, 'type' => 'textarea'],
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
            if (!$data['categories_id']) {
                $data['categories_id'] = 0;
            }

            if ($request->get('duplicate')) {
                $data['slug'] = null;
            }
            $data['slug'] = $this->getSlug($data);
            $result = CurrentModel::create($data);
            $this->setUrlRedirects($result);
            $this->media($request, $result);
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
            array_push($this->formData, 'created_at');
            $result = CurrentModel::select($this->formData)->with(['images'])->find($id);
            if (!$result) {
                return $this->responseError('404');
            }
            $response = [
                'result' => $result,
                'banner' => $result->images()->where(['group_id' => 2])->first(),
                'template' => $this->module,
                'menus' => Menus::getListMenuView(),
                'layout' => 2,
                'breadcrumb' => [
                    ['link' => '', 'label' => \trans('nksoft::common.Home')],
                    ['link' => $result->category->slug, 'label' => $result->category->name],
                    ['link' => '#', 'label' => $result->name],
                ],
                'seo' => $this->SEO($result),
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
            \array_push($this->formData, 'banner');
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
            if (!$data['categories_id']) {
                $data['categories_id'] = 0;
            }
            $data['slug'] = $this->getSlug($data);
            foreach ($data as $k => $v) {
                $result->$k = $v;
            }
            $result->save();
            $this->setUrlRedirects($result);
            $this->media($request, $result);
            $response = [
                'result' => $result,
            ];
            return $this->responseSuccess($response);
        } catch (\Exception $e) {
            return $this->responseError($e);
        }
    }

}
