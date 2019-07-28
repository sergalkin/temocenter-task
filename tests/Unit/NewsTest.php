<?php

namespace Tests\Unit;

use App\Http\Repositories\NewsRepository;
use App\Models\News;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewsTest extends TestCase
{
    use DatabaseMigrations;

    protected $news;
    protected $repository;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function repository_can_return_all_news()
    {
        $this->news = factory('App\Models\News', 5)->create();
        $this->repository = new NewsRepository(new News);
        $this->assertCount(5, $this->repository->all());
    }

    /** @test */
    public function repository_returns_empty_json_if_no_news_found()
    {
        $this->repository = new NewsRepository(new News());
        $this->assertEmpty($this->repository->all());
    }

    /** @test */
    public function repository_can_show_news_by_id()
    {
        $this->news = factory('App\Models\News', 5)->create();
        $this->repository = new NewsRepository(new News);
        $this->assertInstanceOf('App\Http\Resources\News', $this->repository->get(4));
    }

    /** @test */
    public function repository_returns_json_response_on_fail_while_searching_by_id()
    {
        $this->news = factory('App\Models\News', 5)->create();
        $this->repository = new NewsRepository(new News);
        $this->assertEquals('{"data":false}', $this->repository->get(6)->content());
    }


    /** @test */
    public function repository_can_create_new_news_without_published_date()
    {
        $data = ['title' => 'test', 'preview' => 'text', 'body' => 'textcont'];

        $this->repository = new NewsRepository(new News);

        $this->assertEquals('{"data":true}', $this->repository->create($data)->content());

    }

    /** @test */
    public function repository_can_create_new_news_with_published_date()
    {
        $data = ['title' => 'test', 'preview' => 'text', 'body' => 'textcont', 'publication_date' => Carbon::now()];

        $this->repository = new NewsRepository(new News);

        $this->assertEquals('{"data":true}', $this->repository->create($data)->content());
    }

    /** @test */
    public function repository_return_json_object_on_empty_data_while_creating_new_news()
    {
        $data = [];

        $this->repository = new NewsRepository(new News);

        $this->assertEquals('{"data":false}', $this->repository->create($data)->content());
    }

    /** @test */
    public function repository_can_update_news()
    {
        $this->news = factory('App\Models\News')->create();
        $this->repository = new NewsRepository(new News);
        $data = ['title' => 'new title', 'body' => 'new body'];
        $this->repository->update(1, $data);
        $this->assertEquals('new title', $this->repository->get(1)->title);
        $this->assertEquals('new body', $this->repository->get(1)->body);
    }

    /** @test */
    public function repository_returns_json_on_empty_data_while_trying_to_update_entity()
    {
        $this->news = factory('App\Models\News')->create();
        $this->repository = new NewsRepository(new News);

        $this->assertEquals('{"data":false}', $this->repository->update(1, [])->content());
    }

    /** @test */
    public function repository_can_delete_entity()
    {
        $this->news = factory('App\Models\News', 2)->create();
        $this->repository = new NewsRepository(new News);
        $this->repository->delete(1);
        $this->assertCount(1, $this->repository->all());

    }
}
