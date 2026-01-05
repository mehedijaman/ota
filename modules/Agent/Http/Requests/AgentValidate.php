<?php

namespace Modules\Agent\Http\Requests;

use Modules\Support\Http\Requests\Request;

class AgentValidate extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
