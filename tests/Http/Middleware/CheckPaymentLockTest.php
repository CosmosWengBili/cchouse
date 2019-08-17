<?php
namespace Tests\Http\Middleware;

use Carbon\Carbon;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\CheckPaymentLock;


class CheckPaymentLockTest extends TestCase
{
    protected $defaultHeaders;

    protected function setUp(): void
    {
        parent::setUp();

        \Route::get('/previousPage', function () {
            return 'previousPage';
        });
        \Route::middleware('payment.lock')->any('/_test/payments', function () {
            return 'OK';
        });
        $this->defaultHeaders = ['HTTP_REFERER' => '/previousPage'];
    }

    /** @test */
    public function it_redirect_back_when_invalid_date()
    {
        $methods = CheckPaymentLock::CHECK_METHODS;
        $keys = CheckPaymentLock::CHECK_KEYS;
        $invalidDate = (new Carbon('3 months ago'))->toDateString();
        foreach ($keys as $key) {
            foreach ($methods as $method) {
                $response = $this->followingRedirects()
                                 ->post('/_test/payments', [$key => $invalidDate], $this->defaultHeaders);
                $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
                $this->assertEquals('previousPage', $response->getContent());
            }
        }
    }

    /** @test */
    public function it_allow_when_third_day_of_this_month()
    {
        $thirdDay = Carbon::create(2019, 8, 3, 23, 59, 59);
        Carbon::setTestNow($thirdDay);

        $methods = CheckPaymentLock::CHECK_METHODS;
        $keys = CheckPaymentLock::CHECK_KEYS;
        $invalidDate = (new Carbon('1 months ago'))->toDateString();
        foreach ($keys as $key) {
            foreach ($methods as $method) {
                $response = $this->followingRedirects()
                                 ->post('/_test/payments', [$key => $invalidDate], $this->defaultHeaders);
                $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
                $this->assertEquals('OK', $response->getContent());
            }
        }
    }

    /** @test */
    public function it_redirect_back_when_forth_day_of_this_month()
    {
        $thirdDay = Carbon::create(2019, 8, 4, 0, 0, 0);
        Carbon::setTestNow($thirdDay);

        $methods = CheckPaymentLock::CHECK_METHODS;
        $keys = CheckPaymentLock::CHECK_KEYS;
        $invalidDate = (new Carbon('1 months ago'))->toDateString();
        foreach ($keys as $key) {
            foreach ($methods as $method) {
                $response = $this->followingRedirects()
                                 ->post('/_test/payments', [$key => $invalidDate], $this->defaultHeaders);
                $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
                $this->assertEquals('previousPage', $response->getContent());
            }
        }
    }
}
