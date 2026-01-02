<?php

namespace Modules\Faq\Http\Requests;

use Modules\Support\Http\Requests\Request;

class FaqValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
