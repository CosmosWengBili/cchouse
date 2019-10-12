<?php

namespace App\Observers;

use App\Deposit;
use App\EditorialReview;
use App\Notifications\TextNotify;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DepositObserver
{
    /**
     * Handle the share holder "created" event.
     *
     * @param  \App\Deposit  $deposit
     * @return void
     */
    public function created(Deposit $deposit)
    {
        //
    }

    /**
     * Handle the share holder "updated" event.
     *
     * @param  \App\Deposit  $deposit
     * @return void
     */
    public function updated(Deposit $deposit)
    {
        //
    }

    public function updating(Deposit $deposit)
    {
        EditorialReview::create([
            'editable_id' => $deposit->id,
            'editable_type' => Deposit::class,
            'original_value' => collect($deposit->getOriginal())->toArray(),
            'edit_value' => $deposit->getAttributes(),
            'edit_user' => Auth::id(),
            'comment' => '',
        ]);

        // 通知特定使用者做調整
        $user = User::find(1);
        $this->notifySpecialUser($user, $deposit);
    }


    /**
     * Handle the share holder "deleted" event.
     *
     * @param  \App\Deposit  $deposit
     * @return void
     */
    public function deleted(Deposit $deposit)
    {
        //
    }

    /**
     * Handle the share holder "restored" event.
     *
     * @param  \App\Deposit  $deposit
     * @return void
     */
    public function restored(Deposit $deposit)
    {
        //
    }

    /**
     * Handle the share holder "force deleted" event.
     *
     * @param  \App\Deposit  $deposit
     * @return void
     */
    public function forceDeleted(Deposit $deposit)
    {
        //
    }

    /**
     * @param User $user
     * @param Deposit $deposit
     */
    private function notifySpecialUser(User $user, Deposit $deposit)
    {
        $now = Carbon::now();
        $content = "訂金({編號: {$deposit->id}，備註: {$deposit->comment}) 資料已於 {$now} 被更新，請立即前往確認。";
        $user->notify(new TextNotify($content));
    }
}
