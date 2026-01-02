<?php

namespace Modules\Medical\Http\Requests;

use Modules\Support\Http\Requests\Request;

class MedicalValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
