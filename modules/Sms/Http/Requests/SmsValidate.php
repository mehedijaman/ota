<?php

namespace Modules\Sms\Http\Requests;

use Modules\Support\Http\Requests\Request;

class SmsValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
