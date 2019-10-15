<?php
namespace Tests\Unit\Http\Middleware;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\CheckPaymentLock;

class DummyController extends Controller {
    public function store() {
        return response('OK');
    }
}

class CheckPaymentLockTest extends TestCase
{
    protected $defaultHeaders;

    protected function setUp(): void
    {
        parent::setUp();

        \Route::get('/previousPage', function () {
            return 'previousPage';
        });

        $this->app->bind('DummyController', DummyController::class);
        \Route::middleware('payment.lock')->post('/_test/payments', 'DummyController@store');
        $this->defaultHeaders = ['HTTP_REFERER' => '/previousPage'];
    }

    /** @test */
    public function it_redirect_back_when_invalid_date()
    {
        $keys = CheckPaymentLock::CHECK_KEYS;
        $invalidDate = (new Carbon('3 months ago'))->toDateString();
        foreach ($keys as $key) {
            $response = $this->followingRedirects()
                             ->post('/_test/payments', [$key => $invalidDate], $this->defaultHeaders);
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertEquals('previousPage', $response->getContent());
        }
    }

    /** @test */
    public function it_allow_when_third_day_of_this_month()
    {
        $thirdDay = Carbon::create(2019, 8, 3, 23, 59, 59);
        Carbon::setTestNow($thirdDay);

        $keys = CheckPaymentLock::CHECK_KEYS;
        $invalidDate = (new Carbon('1 months ago'))->toDateString();
        foreach ($keys as $key) {
            $response = $this->followingRedirects()
                             ->post('/_test/payments', [$key => $invalidDate], $this->defaultHeaders);
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertEquals('OK', $response->getContent());
        }
    }

    /** @test */
    public function it_redirect_back_when_forth_day_of_this_month()
    {
        $thirdDay = Carbon::create(2019, 8, 4, 0, 0, 0);
        Carbon::setTestNow($thirdDay);

        $keys = CheckPaymentLock::CHECK_KEYS;
        $invalidDate = (new Carbon('1 months ago'))->toDateString();
        foreach ($keys as $key) {
            $response = $this->followingRedirects()
                             ->post('/_test/payments', [$key => $invalidDate], $this->defaultHeaders);
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertEquals('previousPage', $response->getContent());
        }
    }
}
