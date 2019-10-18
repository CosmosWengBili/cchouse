<?php

namespace App\Observers;

use App\Building;
use App\Classes\NotifyUsers;
use App\Classes\TextContent;
use App\EditorialReview;
use App\Shareholder;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
        if( isset($editorialReview->edit_value['command']) && !isset($editorialReview->edit_value['id'])){
            $command = $editorialReview->edit_value['command'];
            if( $command == '刪除' ){
                $this->doModelDelete($editorialReview);
            }
        }
        else{
            switch ($type) {
                case 'App\Shareholder':
                    $this->notifyAfterUpdatedEditableTypeIsShareHolder($editorialReview);
                    if ($editorialReview->status === '已通過') {
                        $model = $this->doModelUpdate($editorialReview);
                        $this->doShareholderUpdate($editorialReview, $model);
                    }
                    break;
                default:
                    if ($editorialReview->status === '已通過') {
                        $model = $this->doModelUpdate($editorialReview);
                    }
                break;                
            }
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
     * 當編輯 shareholder 資料時 不直接更新該筆資料 而是將該筆更新內容儲存至 EditorialReview
     * 然後通知相關人員
     * @param EditorialReview $editorialReview
     */
    private function notifyAfterCreatedEditableTypeIsShareHolder(EditorialReview $editorialReview)
    {
        $now = now();
        $editor = User::find(auth()->user()->id);

        if ($editor) {
            if ($editor->belongsToDepartment('管理處')) {
                $content = "管理處 {$editor->name} 已於 {$now} 編輯一筆股東相關的資料 已進入待審核";
            } elseif ($editor->belongsToDepartment('帳務處')) {
                $content = "帳務處 {$editor->name} 已於 {$now} 編輯一筆股東相關的資料 已進入待審核";
            } else {
                $content = "{$editor->name} 已於 {$now} 編輯一筆股東相關的資料 已進入待審核";
            }

            $content = new TextContent($content);
            (new NotifyUsers($editor))->notifyOneUser(User::first(), $content);
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
        $editor = User::find(auth()->user()->id);

        $url = route('editorialReviews.show', $editorialReview->id);
        $notify = new NotifyUsers($editor);
        $content = new TextContent("您發起的修改「{$nowStatus}」");
        $content->makeUrl('連結', $url);
        $notify->notifySelf($content);

        // 只有在更新 status 時
        if ($nowStatus !== $oldStatus && $nowStatus === '不通過') {
        } elseif ($nowStatus !== $oldStatus && $nowStatus === '已通過') {
            // 通知管理單位及帳務單位
            $notify = new NotifyUsers($editor);
            $now = Carbon::now();
            $name = collect($editorialReview)->get('original_value')['name'];
            $content = (new TextContent())->setContent("股東( {$name} )資料已於 {$now} 審核通過。");
            $notify->notifyByDepartment($content);
        }
    }

    /**
     * 審核通過做更新
     * @param EditorialReview $editorialReview
     *
     * @return mixed
     */
    private function doModelUpdate(EditorialReview $editorialReview)
    {
        // 執行原本應該做的更新
        $model = $editorialReview->editable->find($editorialReview->editable_id);
        $model->update($editorialReview->edit_value);

        return $model;
    }
    /**
     * 審核通過做刪除
     * @param EditorialReview $editorialReview
     *
     * 
     */
    private function doModelDelete(EditorialReview $editorialReview)
    {
        // 執行原本應該做的刪除
        $model = $editorialReview->editable->find($editorialReview->editable_id);
        $model->delete();
    }

    /**
     * @param EditorialReview $editorialReview
     * @param Model           $model
     */
    private function doShareholderUpdate(EditorialReview $editorialReview, Model $model)
    {
        $extra = $editorialReview->extra_data;
        $building_code = array_wrap($extra['building_code']);
        // get building ids by building_code
        $building_ids = Building::whereIn('building_code', $building_code)->get()->pluck('id')->toArray();
        if (! empty($building_ids)) {
            $model->buildings()->sync($building_ids);
        } else {
            $model->buildings()->sync([]);
        }
    }
}
