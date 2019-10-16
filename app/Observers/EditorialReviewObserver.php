<?php

namespace App\Observers;

use App\Building;
use App\EditorialReview;
use App\Notifications\TextNotify;
use App\Shareholder;
use App\User;
use Carbon\Carbon;

class EditorialReviewObserver
{
    /**
     * 每當有一筆審核資料被新增的時候 新增完畢後需要做的事情寫在這裡
     *
     * @param  \App\EditorialReview  $editorialReview
     * @return void
     */
    public function created(EditorialReview $editorialReview)
    {
        /** @var string $type */
        $type = $editorialReview->editable_type;
        switch ($type) {
            case 'App\Shareholder':
                $this->notifyAfterCreatedEditableTypeIsShareHolder($editorialReview);
                break;
        }
    }

    /**
     * 表editorial_reviews 正常只能修改/更新 「審核狀態」
     *
     * @param  \App\EditorialReview  $editorialReview
     * @return void
     */
    public function updated(EditorialReview $editorialReview)
    {
        /** @var string $type */
        $type = $editorialReview->editable_type;
        switch ($type) {
            case 'App\Shareholder':
                $this->notifyAfterUpdatedEditableTypeIsShareHolder($editorialReview);
                if ($editorialReview->status === '已通過') {
                    $this->doShareholderUpdate($editorialReview);
                }
                break;
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

    /**
     * 通知當前審核人員
     * @param EditorialReview $editorialReview
     * @param string          $status
     * @param string          $url
     */
    private function notifySelf(EditorialReview $editorialReview, string $status, string $url='')
    {
        $now = Carbon::now();
        $authId = auth()->id();

        /** @var User $user */
        $user = User::find($authId);
        if ($user) {
            if ($url === '') {
                $content = "您發起的修改「{$status}」({$now})";
            } else {
                $content = "您發起的修改「{$status}」，請查看此<a href='{$url}'>連結</a>({$now})";
            }
            $user->notify(
                new TextNotify($content)
            );
        }
    }

    /**
     * 通知編輯人員
     * @param EditorialReview $editorialReview
     * @param string          $status
     * @param string          $url
     */
    private function notifyEditor(EditorialReview $editorialReview, string $status, string $url='')
    {
        $now = Carbon::now();

        /** @var User $user */
        $user = User::find($editorialReview->edit_user);
        if ($user) {
            if ($url === '') {
                $content = "您發起的修改「{$status}」({$now})";
            } else {
                $content = "您發起的修改「{$status}」，請查看此<a href='{$url}'>連結</a>({$now})";
            }
            $user->notify(
                new TextNotify($content)
            );
        }
    }

    /**
     * 當編輯 shareholder 資料時 不直接更新該筆資料 而是將該筆更新內容儲存至 EditorialReview
     * 然後通知相關人員
     * @param EditorialReview $editorialReview
     */
    private function notifyAfterCreatedEditableTypeIsShareHolder(EditorialReview $editorialReview)
    {
        $now = now();
        $editor = auth()->user();

        if ($editor) {
            if ($editor->belongsToDepartment('管理處')) {
                $content = "管理處 {$editor->name} 已於 {$now} 編輯一筆房東相關的資料 已進入待審核";
            } elseif ($editor->belongsToDepartment('帳務處')) {
                $content = "帳務處 {$editor->name} 已於 {$now} 編輯一筆房東相關的資料 已進入待審核";
            } else {
                $content = "{$editor->name} 已於 {$now} 編輯一筆房東相關的資料 已進入待審核";
            }

            User::first()->notify(
                new TextNotify($content)
            );
        }
    }

    /**
     * 當編輯審核 通過/不通過 的時候 要做的事情
     * @param EditorialReview $editorialReview
     */
    private function notifyAfterUpdatedEditableTypeIsShareHolder(EditorialReview $editorialReview)
    {
        $nowStatus = $editorialReview->status;
        $oldStatus = $editorialReview->getOriginal('status');

        $url = route('editorialReviews.show', $editorialReview->id);
        $this->notifyEditor($editorialReview, $nowStatus, $url);

        // 只有在更新 status 時
        if ($nowStatus !== $oldStatus && $nowStatus === '不通過') {

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

    private function doShareholderUpdate(EditorialReview $editorialReview)
    {
        // 執行原本應該做的更新
        $extra = $editorialReview->extra_data;
        $shareholder = Shareholder::find($editorialReview->editable_id);

        $shareholder->update($editorialReview->edit_value);

        $building_code = array_wrap($extra['building_code']);
        // get building ids by building_code
        $building_ids = Building::whereIn('building_code', $building_code)->get()->pluck('id')->toArray();
        if (! empty($building_ids)) {
            $shareholder->buildings()->sync($building_ids);
        } else {
            $shareholder->buildings()->sync([]);
        }
    }


}
