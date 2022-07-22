<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resource>
 */
class ResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'properties' => json_encode($this->faker->rgbColorAsArray, 128),
        ];
    }

    public function pdfResource(): ResourceFactory
    {
        return $this->state(function (array $attributes) {
            return [
              'name' => 'pdf',
              'properties' => [
                  'title' => 'A pdf file title',
                  'path' => $this->faker->url,
              ],
            ];
        });
    }

    public function htmlResource(): ResourceFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'html',
                'properties' => [
                    'title' => 'A html resource',
                    'description' => $this->faker->text,
                    'snippet' => $this->faker->text,
                ],
            ];
        });
    }

    public function linkResource(): ResourceFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'link',
                'properties' => [
                    'title' => 'A pdf file title',
                    'link' => $this->faker->url,
                ],
            ];
        });
    }
}
