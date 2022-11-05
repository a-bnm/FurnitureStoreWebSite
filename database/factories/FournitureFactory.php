<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\odel=Fourniture>
 */
class FournitureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name'=>$this->faker->name,
            'small_description'=>$this->faker->small_description,
            'description'=>$this->faker->description,
            'price'=>$this->faker->price,
            'quantity'=>$this->faker->quantity,
            'image1'=>$this->faker->image1,
            'image2'=>$this->faker->image2,
            'image3'=>$this->faker->image3
        ];
    }
}
