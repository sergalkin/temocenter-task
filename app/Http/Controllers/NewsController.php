<?php

namespace App\Http\Controllers;

use App\Http\Repositories\NewsRepository;
use App\Http\Requests\NewsRequest;
use App\Models\News;
use Illuminate\Http\Request;


class NewsController extends Controller
{
    private $model;

    /**
     * NewsController constructor.
     *
     * @param News $news
     */
    public function __construct(News $news)
    {
        $this->model = new NewsRepository($news);
    }


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(Request $request)
    {
        return $this->model->all();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param NewsRequest $request
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
     * @param News $news
     * @param $id
     * @return \App\Http\Resources\News|\Illuminate\Http\JsonResponse
     */
    public function show(News $news, $id)
    {
        return $this->model->get($id);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param NewsRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(NewsRequest $request, $id)
    {
        $request->validated();
        return $this->model->update($id, $request->all());
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->model->delete($id);
    }
}
