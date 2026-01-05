<?php

namespace Modules\Sms\Services;

class SmsPartsService
{
    public function parts(string $message): int
    {
        $length = mb_strlen($message);

        $isUnicode = (bool) preg_match('/[^\x00-\x7F]/u', $message);

        $singlePartLimit = $isUnicode ? 70 : 160;
        $multiPartLimit = $isUnicode ? 67 : 153;

        if ($length <= $singlePartLimit) {
            return 1;
        }

        return (int) max(1, (int) ceil($length / $multiPartLimit));
    }
}
