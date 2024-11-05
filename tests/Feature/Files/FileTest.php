<?php

namespace Tests\Feature\Files;

use Illuminate\Http\UploadedFile;
use Tests\ItemTest;
use App\Models\Files\File;
use PHPUnit\Framework\Attributes\Test;

class FileTest extends ItemTest
{
    protected $validStructure = [
        'data' => [
            'id',
            'type',
            'original_name',
            'mime_type',
            'extension',
            'size',
            'url',
            'title',
        ],
    ];
    protected $uri = '/api/files';
    protected $class = File::class;
    protected $searchString = 'image';

    public function setUp(): void
    {
        parent::setUp();
        $this->dummyData = [
            'original_name' => 'image.jpg',
        ];
        $this->dummyTranslatableData = [
            'title:ru' => 'картинка',
        ];
        $this->itemAttributes = [
            'original_name' => 'image.jpg',
        ];
    }

    #[Test]
    public function create_item()
    {
        $dummyData = [
            'filable_type' => 'Access\\User',
            'filable_id' => $this->user->id,
            'type' => 'avatar',
        ];
        $additionalData = [
            'file' => UploadedFile::fake()->image('image.jpg', 1024, 768),
        ];
        $response = $this->actingAs($this->user)
            ->postJson($this->uri, array_merge($dummyData, $additionalData))
            ->assertSuccessful()
            ->assertJsonStructure($this->validStructure);

        $dummyData['filable_type'] = 'App\\Models\\Access\\User';
        $this->assertDatabaseHas($this->tableName, $dummyData);

        return $response;
    }
}
