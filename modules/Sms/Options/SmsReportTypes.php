<?php

namespace Modules\Sms\Options;

class SmsReportTypes
{
    /**
     * Get all SMS report types
     */
    public static function get(): array
    {
        return [
            [
                'value' => 'history',
                'label' => 'SMS Send History',
            ],
            [
                'value' => 'usage',
                'label' => 'Usage Summary',
            ],
        ];
    }

    /**
     * Get only the list of values
     */
    public static function getValues(): array
    {
        return array_column(self::get(), 'value');
    }
}
