<?php

namespace App;

use App\Notifications\Formatters\BaseFormatter;
use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;


class DatabaseNotification extends BaseDatabaseNotification
{
    protected static function findFormatter(DatabaseNotification $notification)
    {
        foreach (BaseFormatter::allFormatters() as $formatter) {
            if ($formatter::canFormat($notification)) {
                return $formatter;
            }
        }

        return 'App\Notifications\Formatters\NullFormatter';
    }

    public function header(): string
    {
        return $this->formatter()->header();
    }

    public function content(): string
    {
        return $this->formatter()->content();
    }

    private function formatter(): BaseFormatter {
        $formatterClass = self::findFormatter($this);
        return new $formatterClass($this);
    }

}
