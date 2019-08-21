<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\SystemVariable;

class CheckPaymentLock
{
    const CHECK_KEYS = [
        'due_time', # 應繳時間
        'paid_at',  # 匯款時間
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $enabled = SystemVariable::get('Payment', 'PaymentLock');
        if ($enabled) {
            if (!$this->checkIsValid($request)) {
                return Redirect::back()->withErrors(['無法建立「應繳時間」或「匯款時間」為本月之前的記錄，請聯絡系統管理員解除鎖定。']);
            }
        }

        return $next($request);
    }

    public function checkIsValid(Request $request) {
        if (!$this->needToCheck($request)) {
            return true;
        }

        foreach (self::CHECK_KEYS as $key) {
            $dueTimeStr = $request->input($key);
            if(is_null($dueTimeStr)) {
                continue;
            }

            $dueTime = Carbon::Parse($dueTimeStr);
            $nowDayOrdinal = (new Carbon())->day;

            // 超過三號僅可建立本月資料
            if ($nowDayOrdinal > 3) {
                $limit = (new Carbon('first day of this month'))->startOfDay();
            } else {
                $limit = (new Carbon('first day of last month'))->startOfDay();
            }

            if ($dueTime->isBefore($limit)) {
                return false;
            }
        }

        return true;
    }

    private function needToCheck(Request $request) {
        $action = $request->route()->getActionMethod();

        switch ($action) {
            case 'update':
            case 'store':
                return true;
                break;
            case 'index':
            case 'show':
            default:
                return false;
        }
    }
}
