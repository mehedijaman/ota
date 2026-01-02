<?php

namespace Modules\Visa\Http\Requests;

use Modules\Support\Http\Requests\Request;

class VisaValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
