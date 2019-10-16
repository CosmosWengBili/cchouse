<?php
/**
 * Created by PhpStorm.
 * User: lichengxiu
 * Date: 2019-10-16
 * Time: 13:35
 */

namespace App\Classes;


use App\Notifications\TextNotify;
use App\User;

class NotifyUsers
{
    /** @var User $editor */
    private $editor;

    public function __construct(User $editor)
    {
        $this->editor = $editor;
    }

    /**
     * 通知單一人員
     * @param User        $user
     * @param TextContent $content
     */
    public function notifyOneUser(User $user, TextContent $content)
    {
        $user->notify(
            new TextNotify($content->finalResult())
        );
    }

    /**
     * 通知群組內的所有人員
     * @param string      $groupName
     * @param TextContent $content
     */
    public function notifyByGroup(string $groupName, TextContent $content)
    {
        User::group($groupName)->get()->each(function (User $user) use ($content) {
            $user->notify(
                new TextNotify($content->finalResult())
            );
        });
    }

    /**
     * 通知部門所有人員
     * @param TextContent $content
     */
    public function notifyByDepartment(TextContent $content)
    {
        // 通知管理單位及帳務單位
        User::get()->each(function (User $user) use ($content) {
            if ($user->belongsToDepartment('管理處')) {
                $content->contentFirst('管理處通知: ');
                $this->notifyOneUser($user, $content);
            } elseif ($user->belongsToDepartment('帳務處')) {
                $content->contentFirst('帳務處通知: ');
                $this->notifyOneUser($user, $content);
            }
        });
    }

    /**
     * 通知系統內所有人員
     * @param TextContent $content
     * @param bool $onlyHasDepartment 是否只通知有部門的人員
     */
    public function notifyAll(TextContent $content, bool $onlyHasDepartment=true)
    {
        // 通知管理單位及帳務單位
        User::get()->each(function (User $user) use ($content, $onlyHasDepartment) {
            if ($user->belongsToDepartment('管理處')) {
                $content->contentFirst('管理處通知: ');
                $this->notifyOneUser($user, $content);
            } elseif ($user->belongsToDepartment('帳務處')) {
                $content->contentFirst('帳務處通知: ');
                $this->notifyOneUser($user, $content);
            } else {
                if (! $onlyHasDepartment) {
                    $this->notifyOneUser($user, $content);
                }
            }
        });
    }

    /**
     * 通知自己
     * @param TextContent $content
     */
    public function notifySelf(TextContent $content)
    {
        $this->editor->notify(
            new TextNotify($content->finalResult())
        );
    }

}
