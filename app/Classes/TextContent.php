<?php
/**
 * Created by PhpStorm.
 * User: lichengxiu
 * Date: 2019-10-16
 * Time: 13:39
 */

namespace App\Classes;


use Carbon\Carbon;
use Illuminate\Support\Arr;

class TextContent
{
    /** @var array */
    private $content;
    private $withOutDate;
    private $now;
    private $urls;
    private $contentGlue;

    public function __construct(string $content='')
    {
        $this->content[] = $content;
        $this->withOutDate = false;
        $this->now = Carbon::now()->format('Y-m-d H:i:s');
        $this->urls = [];
        $this->contentGlue = '<br>';
    }

    /**
     * 設置通知的內容
     * @param string $content
     *
     * @return self
     */
    public function setContent(string $content)
    {
        return tap($this, function (TextContent $instance) use ($content) {
            if ($content !== '') {
                $instance->content[] = $content;
            }
        });
    }

    public function contentFirst(string $content)
    {
        return tap($this, function (TextContent $instance) use ($content) {
            if ($content !== '') {
                Arr::prepend($instance->content, $content);
            }
        });
    }

    /**
     * 清除所有通知的內容
     * @return mixed
     */
    public function cleanContent()
    {
        return tap($this, function (TextContent $instance) {
            $instance->content = [];
            $instance->urls = [];
        });
    }

    /**
     * 取得目前通知內容的陣列
     * 發送的內容應該跟陣列內容相符合
     * @return array
     */
    public function getContent()
    {
        return $this->withOutDate
            ? $this->content
            : array_merge($this->content, $this->urls, [$this->now]);
    }

    /**
     * 設置是否通知內帶上日期
     * @param bool $withOutDate
     *
     * @return mixed
     */
    public function withOutDate(bool $withOutDate = false)
    {
        return tap($this, function (TextContent $instance) use ($withOutDate) {
            $instance->withOutDate = $withOutDate;
        });
    }

    /**
     * 產生網頁上的連結 支援多個連結
     * @param string $text
     * @param string $url
     *
     * @return string
     */
    public function makeUrl(string $text, string $url)
    {
        return tap($this, function ($instance) use ($text, $url) {
            $instance->urls[] = "<a href='{$url}'>{$text}</a>";
        });
    }

    /**
     * 清除所有連結
     * @return mixed
     */
    public function cleanUrls()
    {
        return tap($this, function (TextContent $instance) {
            $instance->urls = [];
        });
    }

    /**
     * 返回最終的通知內容
     * @return string
     */
    public function finalResult()
    {
        $content = collect($this->getContent())->filter(function ($item) {
            return ! empty($item);
        })
            ->toArray();
        return implode($this->contentGlue, $content);
    }
}
