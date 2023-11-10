<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status' => '1', 
            'created_user_id' => $this->faker->randomNumber, 
            'updated_user_id' => $this->faker->randomNumber, 
            'deleted_user_id' => null,
        ];
    }
}
