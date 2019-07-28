<?php

namespace App\Http\Controllers;

use App\Http\Repositories\NewsRepository;
use App\Http\Requests\NewsRequest;
use App\Models\News;
use Illuminate\Http\Request;


class NewsController extends Controller
{
    private $model;

    public function __construct(News $news)
    {
        //$this->middleware('auth:api')->except('index');
        $this->model = new NewsRepository($news);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(Request $request)
    {
        return $this->model->all();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(NewsRequest $request)
    {
        $request->validated();
        return $this->model->create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param \App\News $news
     * @return \Illuminate\Http\Response
     */
    public function show(News $news, $id)
    {
        return $this->model->get($id);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\News $news
     * @return \Illuminate\Http\Response
     */
    public function update(NewsRequest $request, $id)
    {
        $request->validated();
        return $this->model->update($id, $request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\News $news
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->model->delete($id);
    }
}
