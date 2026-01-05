<?php

namespace Modules\Employee\Imports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Employee\Models\Designation;

class DesignationImport implements ToModel, WithHeadingRow
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Helper function to safely access array values
            $getValue = function ($key) use ($row) {
                return array_key_exists($key, $row) ? $row[$key] : null;
            };

            // Look up the ID for mandatory fields
            $code = $getValue('code');
            $name = $getValue('name');

            // Check if mandatory fields are present and not blank
            if (blank($name)) {
                Log::error('Skipping row due to missing mandatory field: name', ['name' => $name]);

                return null;
            }

            if ($code) {
                // Check if the code already exists in the Designation table
                $existingDesignation = Designation::where('code', $code)->first();

                if ($existingDesignation) {
                    Log::info('Skipping row because designation code already exists', ['code' => $code]);

                    return null;
                }
            }

            if ($name) {
                // Check if the code already exists in the Designation table
                $existingDesignation = Designation::where('name', $name)->first();

                if ($existingDesignation) {
                    Log::info('Skipping row because designation name already exists', ['name' => $name]);

                    return null;
                }
            }

            // Prepare designation data array
            $designation = [
                'name' => $name,
                'code' => $code,
            ];

            // Attempt to create the new designation
            return new Designation($designation);
        } catch (\Exception $e) {
            // Log error with row data and exception message
            Log::error('Error creating designation', [
                'row' => $row,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
