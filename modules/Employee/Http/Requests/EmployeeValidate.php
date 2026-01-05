<?php

namespace Modules\Employee\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Modules\Employee\Models\Designation;
use Modules\Employee\Models\Employee;
use Modules\Support\Http\Requests\Request;

class EmployeeValidate extends Request
{
    public function rules(): array
    {
        $employee = $this->route('employee');

        return [

            'designation_id' => [
                'nullable',
                'integer',
                Rule::exists(Designation::class, 'id'),
            ],

            'code' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^\S*$/', // This regex ensures no spaces, tabs, or newlines
                Rule::unique(Employee::class)
                    ->ignore($employee?->id),
            ],

            'joining_date' => 'nullable|date',

            'name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'marital_status' => 'nullable|string|in:Single,Married,Divorced,Widowed',
            'spouse_name' => 'nullable|string|max:255',

            'nid_no' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique(Employee::class)
                    ->ignore($employee?->id),
            ],

            'passport_no' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique(Employee::class)
                    ->ignore($employee?->id),
            ],

            'dl_no' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique(Employee::class)
                    ->ignore($employee?->id),
            ],

            'gender' => 'nullable|string|in:Male,Female,Other',
            'religion' => 'nullable|string|max:255',
            'blood_group' => 'nullable|string|in:A+,A-,B+,B-,O+,O-,AB+,AB-',

            'phone' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'email',
                Rule::unique(Employee::class)
                    ->ignore($employee?->id),
                function ($attribute, $value, $fail) use ($employee) {
                    // Keep employee emails unique against users (users.email is globally unique).
                    // Allow if it's the same linked user (editing).
                    $userExists = User::where('email', $value)
                        ->where('id', '!=', optional($employee?->user)->id)
                        ->exists();

                    if ($userExists) {
                        $fail(__('The email has already been taken.'));
                    }
                },
            ],
            'emergency_contact' => 'nullable|string|max:255',

            'present_address' => 'nullable|string|max:255',
            'permanent_address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',

            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_no' => 'nullable|string|max:255',
            'routing_no' => 'nullable|string|max:255',
            'account_type' => 'nullable|string|max:255',
            'active' => 'nullable|boolean',

            // Photo upload
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_photo' => 'nullable|boolean',

            // Document uploads
            'nid_card' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:5120',
            'remove_nid_card' => 'nullable|boolean',
            'passport' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:5120',
            'remove_passport' => 'nullable|boolean',
            'driving_licence' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:5120',
            'remove_driving_licence' => 'nullable|boolean',
            'tin_certificate' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:5120',
            'remove_tin_certificate' => 'nullable|boolean',
        ];
    }
}
