<?php

namespace Tests\Feature;

use App\Adapters\JwtAdapter;
use App\Adapters\RedisAdapter;
use App\Auth;
use App\Http\Repositories\NewsRepository;
use App\Models\News;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NewsTest extends TestCase
{
    use DatabaseMigrations;

    protected $news;
    protected $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->disableCookiesEncryption('jwt');
        // Без этой строки не работает метод очистки
        (new RedisAdapter('token_test'))->returnConnection();
    }

    /**
     * @uses NewsRepository
     * @uses News
     * @test
     */
    public function user_can_see_all_news()
    {
        $this->news = factory('App\Models\News', 2)->create();
        $this->repository = new NewsRepository(new News);
        $response = $this->get('api/news');
        $response->assertStatus(200);
    }

    /** @test */
    public function user_sees_empty_json_data_if_no_news()
    {
        $response = $this->get('api/news');
        $response->assertJson(['data' => []]);
    }

    /** @test */
    public function user_can_see_exact_news()
    {
        $this->news = factory('App\Models\News')->create();

        $id = $this->news->id;
        $title = $this->news->title;
        $preview = $this->news->preview;
        $body = $this->news->body;

        $getResponse = $this->get("api/news/${id}");
        $getResponse->assertSee($title);
        $getResponse->assertSee($preview);
        $getResponse->assertSee(htmlspecialchars($body));
    }

    /** @test */
    public function user_can_see_json_error_while_addressing_exact_news()
    {
        $this->news = factory('App\Models\News')->create();

        $getResponse = $this->get("api/news/2");
        $getResponse->assertJson(['data' => false]);
    }

    /**
     * @uses JwtAdapter::generateToken()
     * @uses Auth::generateCookie()
     * @test
     */
    public function logged_user_can_create_news()
    {
        factory('App\Models\User')->create();

        $token = (new JwtAdapter('token_test'))->generateToken();
        (new Auth('token_test'))->generateCookie($token);

        $cookie = ['jwt' => $token];
        $data = ['title' => 'fives', 'preview' => 'tensimbols', 'body' => 'fifteen simbols', 'testing' => 'token_test'];
        $response = $this->call('post', 'api/news', $data, $cookie);

        $response->assertJson(['data' => true]);
    }

    /**
     * @uses JwtAdapter::generateToken()
     * @uses Auth::generateCookie()
     * @test
     */
    public function logged_user_cant_create_news_if_not_enough_data_passed()
    {
        factory('App\Models\User')->create();
        $token = (new JwtAdapter('token_test'))->generateToken();
        (new Auth('token_test'))->generateCookie($token);

        $cookie = ['jwt' => $token];

        $data = ['title' => 'test', 'body' => 'text body', 'testing' => 'token_test'];
        $response = $this->call('post', 'api/news', $data, $cookie);

        $this->expectException('Illuminate\Validation\ValidationException');
        $response->assertJsonValidationErrors(['preview']);
    }

    /**
     * @uses JwtAdapter::generateToken()
     * @uses Auth::generateCookie()
     * @test
     */
    public function logged_user_can_update_news()
    {
        factory('App\Models\User')->create();
        $token = (new JwtAdapter('token_test'))->generateToken();
        (new Auth('token_test'))->generateCookie($token);

        $cookie = ['jwt' => $token];

        $this->news = factory('App\Models\News')->create();
        $id = $this->news->id;
        $data = ['title' => 'fives',
            'preview' => 'tensimbols',
            'body' => 'fifteen simbols',
            'testing' => 'token_test'
        ];

        $putResponse = $this->call('put', "api/news/${id}", $data, $cookie);
        $putResponse->assertJson(['data' => true]);

        $getResponse = $this->get("api/news/${id}");
        $getResponse->assertSee("fives");
        $getResponse->assertSee("tensimbols");
        $getResponse->assertSee("fifteen simbols");
    }

    /**
     * @uses JwtAdapter::generateToken()
     * @uses Auth::generateCookie()
     * @test
     */
    public function logged_user_cant_update_news_if_not_enough_data_passed()
    {
        factory('App\Models\User')->create();
        $token = (new JwtAdapter('token_test'))->generateToken();
        (new Auth('token_test'))->generateCookie($token);

        $cookie = ['jwt' => $token];

        $this->news = factory('App\Models\News')->create();
        $id = $this->news->id;
        $data = ['title' => 'test', 'body' => 'text body', 'testing' => 'token_test'];

        $response = $this->call('put', "api/news/${id}", $data, $cookie);

        $this->expectException('Illuminate\Validation\ValidationException');
        $response->assertJsonValidationErrors(['preview']);
    }


    /**
     * @uses JwtAdapter::generateToken()
     * @uses Auth::generateCookie()
     * @test
     */
    public function logged_user_can_delete_news()
    {
        factory('App\Models\User')->create();
        $token = (new JwtAdapter('token_test'))->generateToken();
        (new Auth('token_test'))->generateCookie($token);

        $cookie = ['jwt' => $token];

        $this->news = factory('App\Models\News')->create();
        $id = $this->news->id;
        $delete = $this->call('delete', "api/news/${id}", ['testing' => 'token_test'], $cookie);
        $delete->assertJson(['data' => true]);
    }

    /**
     * @uses JwtAdapter::generateToken()
     * @uses Auth::generateCookie()
     * @test
     */
    public function logged_user_cant_delete_un_existing_news()
    {
        factory('App\Models\User')->create();
        $token = (new JwtAdapter('token_test'))->generateToken();
        (new Auth('token_test'))->generateCookie($token);

        $cookie = ['jwt' => $token];

        $this->news = factory('App\Models\News')->create();

        $delete = $this->call('delete', "api/news/2", ['testing' => 'token_test'], $cookie);
        $delete->assertJson(['data' => false]);
    }

    /** @test */
    public function not_logged_user_cant_create_news()
    {
        $data = ['title' => 'test', 'preview' => 'text', 'body' => 'text body'];
        $response = $this->post('api/news', $data);

        $response->assertJson(['U need to login first, before u can access this route']);
    }

    /** @test */
    public function not_logged_user_cant_update_news()
    {
        $this->news = factory('App\Models\News')->create();
        $id = $this->news->id;
        $data = ['title' => 'test1', 'preview' => 'text1', 'body' => 'text body1'];

        $putResponse = $this->put("api/news/${id}", $data);
        $putResponse->assertJson(["U need to login first, before u can access this route"]);

    }

    /** @test */
    public function not_logged_user_cant_delete_news()
    {
        $this->news = factory('App\Models\News')->create();

        $delete = $this->delete("api/news/2");
        $delete->assertJson(['U need to login first, before u can access this route']);
    }

    /**
     * Удаляем лишний мусор из редиса
     * @afterClass
     */
    public static function delete_garbage()
    {
        (new RedisAdapter('token_test'))->returnConnection()->del('token_test');
    }
}
