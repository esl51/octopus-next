<?php

namespace Database\Factories\Files;

use App\Models\Access\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Files\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type' => 'avatar',
            'filable_id' => User::first()->id,
            'filable_type' => User::class,
            'file_name' => Str::random(32) . '.jpg',
            'original_name' => 'image.jpg',
            'mime_type' => 'images/jpeg',
            'extension' => 'jpg',
            'size' => '102400',
        ];
    }
}
