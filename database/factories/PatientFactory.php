<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        $provinceId = Province::inRandomOrder()->first()?->id;

        return [
            'patient_id' => 'PAT-' . fake()->unique()->numerify('#####'),
            'surname' => fake()->lastName(),
            'given_name' => fake()->firstName(),
            'date_of_birth' => fake()->dateTimeBetween('-80 years', '-18 years'),
            'sex' => fake()->randomElement(['male', 'female']),
            'personal_status' => fake()->randomElement(['single', 'married', 'divorced']),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'province_id' => $provinceId,
            'district_id' => null,
            'community_id' => null,
            'village_id' => null,
            'blood_type' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'medical_notes' => fake()->optional()->sentence(),
            'insurance_info' => fake()->optional()->company(),
            'status' => 'active',
        ];
    }
}
