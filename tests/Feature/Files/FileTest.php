<?php

namespace Tests\Feature\Files;

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
            'title:en' => 'image',
            'title:ru' => 'картинка',
        ];
        $this->itemAttributes = [
            'original_name' => 'image.jpg',
        ];
    }

    #[Test]
    public function create_item()
    {
        $this->markTestSkipped('not needed');
    }
}
