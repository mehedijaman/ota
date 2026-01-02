<?php

namespace Modules\Bill\Http\Requests;

use Modules\Support\Http\Requests\Request;

class BillValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
