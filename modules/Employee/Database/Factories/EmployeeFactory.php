<?php

namespace Modules\Employee\Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Employee\Models\Area;
use Modules\Employee\Models\Department;
use Modules\Employee\Models\Designation;
use Modules\Employee\Models\Employee;
use Throwable;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $this->faker->unique(true);
        // Generate a unique email first
        $email = $this->faker->unique()->safeEmail.$this->faker->randomNumber();

        return [

            'designation_id' => Designation::inRandomOrder()->first()->id ?? Designation::factory()->create()->id,
            'department_id' => Department::inRandomOrder()->first()->id ?? Department::factory()->create()->id,
            'area_id' => Area::inRandomOrder()->first()->id ?? Area::factory()->create()->id,

            'name' => $this->faker->name,

            'joining_date' => $this->faker->date(),
            'birthday' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'phone' => $this->faker->phoneNumber,
            'email' => $email,
            'emergency_contact' => $this->faker->name,
            'present_address' => $this->faker->address,
            'permanent_address' => $this->faker->address,
            // 'photo' => $this->createPhoto(),
            'nid_no' => $this->faker->unique()->numerify('#################'),
            'passport_no' => $this->faker->unique()->numerify('#################'),
            'dl_no' => $this->faker->unique()->numerify('#################'),
            'tin_no' => $this->faker->unique()->numerify('#################'),
            'father_name' => $this->faker->name('male'),
            'mother_name' => $this->faker->name('female'),
            'spouse_name' => $this->faker->optional()->name,
            'marital_status' => $this->faker->randomElement(['Single', 'Married', 'Divorced', 'Widowed']),
            'spouse_name' => $this->faker->optional()->name,
            'blood_group' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
            'religion' => $this->faker->randomElement(['Christianity', 'Islam', 'Hinduism', 'Buddhism', 'Other']),
            'notes' => $this->faker->text,
            'is_active' => $this->faker->boolean(99),

            'created_by' => User::inRandomOrder()->first()->id ?? null,
            'updated_by' => User::inRandomOrder()->first()->id ?? null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    private function createPhoto(): string
    {
        try {
            $photo = file_get_contents('https://picsum.photos/300/200');
        } catch (Throwable $exception) {
            return 'Error fetching image: '.$exception->getMessage();
        }

        $fileName = Str::uuid().'.jpg';

        Storage::disk('public')->put("employee/photo/{$fileName}", $photo);

        return $fileName;
    }
}
