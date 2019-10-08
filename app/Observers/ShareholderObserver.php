<?php

namespace App\Observers;

use App\EditorialReview;
use App\Notifications\TextNotify;
use App\ShareHolder;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ShareholderObserver
{
    /**
     * Handle the share holder "created" event.
     *
     * @param  \App\ShareHolder  $shareHolder
     * @return void
     */
    public function created(ShareHolder $shareHolder)
    {
        //
    }

    /**
     * Handle the share holder "updated" event.
     *
     * @param  \App\ShareHolder  $shareHolder
     * @return void
     */
    public function updated(ShareHolder $shareHolder)
    {
    }

    public function updating(ShareHolder $shareHolder)
    {
        EditorialReview::create([
            'editable_id' => $shareHolder->id,
            'editable_type' => Shareholder::class,
            'original_value' => collect($shareHolder->getOriginal())->toArray(),
            'edit_value' => $shareHolder->getAttributes(),
            'edit_user' => Auth::id(),
            'comment' => '',
        ]);

        // 通知特定使用者做調整
        $user = User::find(1);
        $this->notifySpecialUser($user, $shareHolder);
    }


    /**
     * Handle the share holder "deleted" event.
     *
     * @param  \App\ShareHolder  $shareHolder
     * @return void
     */
    public function deleted(ShareHolder $shareHolder)
    {
        //
    }

    /**
     * Handle the share holder "restored" event.
     *
     * @param  \App\ShareHolder  $shareHolder
     * @return void
     */
    public function restored(ShareHolder $shareHolder)
    {
        //
    }

    /**
     * Handle the share holder "force deleted" event.
     *
     * @param  \App\ShareHolder  $shareHolder
     * @return void
     */
    public function forceDeleted(ShareHolder $shareHolder)
    {
        //
    }

    /**
     * @param User $user
     * @param Shareholder $shareHolder
     */
    private function notifySpecialUser(User $user, Shareholder $shareHolder)
    {
        $now = Carbon::now();
        $content = "股東( {$shareHolder->name} )資料已於 {$now} 被更新，請立即前往確認。";
        $user->notify(
            new TextNotify($content)
        );
    }
}
