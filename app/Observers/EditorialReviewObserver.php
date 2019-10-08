<?php

namespace App\Observers;

use App\EditorialReview;
use App\Notifications\TextNotify;
use App\User;
use Carbon\Carbon;

class EditorialReviewObserver
{
    /**
     * Handle the editorial review "created" event.
     *
     * @param  \App\EditorialReview  $editorialReview
     * @return void
     */
    public function created(EditorialReview $editorialReview)
    {
        //
    }

    /**
     * Handle the editorial review "updated" event.
     *
     * @param  \App\EditorialReview  $editorialReview
     * @return void
     */
    public function updated(EditorialReview $editorialReview)
    {
        $nowStatus = $editorialReview->status;
        $oldStatus = $editorialReview->getOriginal('status');

        // 只有在更新 status 時
        if ($nowStatus !== $oldStatus && $nowStatus === '不通過') {
            // 恢復數據？
        } elseif ($nowStatus !== $oldStatus && $nowStatus === '已通過') {
            // 通知管理單位及帳務單位
            User::get()->each(function (User $user) use ($editorialReview) {
                if ($user->belongsToDepartment('管理處')) {
                    $this->notifyByDepartment($user, $editorialReview, '管理處');
                } elseif ($user->belongsToDepartment('帳務處')) {
                    $this->notifyByDepartment($user, $editorialReview, '帳務處');
                }
            });
        }
    }

    /**
     * Handle the editorial review "deleted" event.
     *
     * @param  \App\EditorialReview  $editorialReview
     * @return void
     */
    public function deleted(EditorialReview $editorialReview)
    {
        //
    }

    /**
     * Handle the editorial review "restored" event.
     *
     * @param  \App\EditorialReview  $editorialReview
     * @return void
     */
    public function restored(EditorialReview $editorialReview)
    {
        //
    }

    /**
     * Handle the editorial review "force deleted" event.
     *
     * @param  \App\EditorialReview  $editorialReview
     * @return void
     */
    public function forceDeleted(EditorialReview $editorialReview)
    {
        //
    }

    /**
     * @param User $user
     * @param EditorialReview $editorialReview
     * @param string $department
     */
    private function notifyByDepartment(User $user, EditorialReview $editorialReview, string $department)
    {
        $now = Carbon::now();
        $name = collect($editorialReview)->get('original_value')['name'];
        $content = "{$department}通知： 股東( {$name} )資料已於 {$now} 審核通過。";
        $user->notify(
            new TextNotify($content)
        );
    }
}
