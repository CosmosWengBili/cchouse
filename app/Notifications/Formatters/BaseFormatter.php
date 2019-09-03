<?php

namespace App\Notifications\Formatters;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

abstract class BaseFormatter {
    protected $notification;

    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    public static function allFormatters(): array {
        $path = __DIR__;
        $formatters = array();

        $allFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $phpFiles = new RegexIterator($allFiles, '/\.php$/');
        foreach ($phpFiles as $phpFile) {
            $content = file_get_contents($phpFile->getRealPath());
            $tokens = token_get_all($content);
            $namespace = '';
            for ($index = 0; isset($tokens[$index]); $index++) {
                if (!isset($tokens[$index][0])) {
                    continue;
                }
                if (T_NAMESPACE === $tokens[$index][0]) {
                    $index += 2; // Skip namespace keyword and whitespace
                    while (isset($tokens[$index]) && is_array($tokens[$index])) {
                        $namespace .= $tokens[$index++][1];
                    }
                }
                if (T_CLASS === $tokens[$index][0] &&
                    T_WHITESPACE === $tokens[$index + 1][0] &&
                    T_STRING === $tokens[$index + 2][0]) {
                    $index += 2; // Skip class keyword and whitespace
                    $formatter = $namespace.'\\'.$tokens[$index][1];;

                    if ($formatter != 'App\Notifications\Formatters\BaseFormatter') {// skip self
                        $formatters[] = $formatter;
                    }

                    # break if you have one class per file (psr-4 compliant)
                    # otherwise you'll need to handle class constants (Foo::class)
                    break;
                }
            }
        }

        return $formatters;
    }

    // 決定這個通知能不能套用這個 formatter
    abstract static function canFormat($notification): bool;

    // 回傳通知標題
    abstract function header(): string;

    // 回傳通知內文
    abstract function content(): string;
}
