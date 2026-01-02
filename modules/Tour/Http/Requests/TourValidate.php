<?php

namespace Modules\Tour\Http\Requests;

use Modules\Support\Http\Requests\Request;

class TourValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
