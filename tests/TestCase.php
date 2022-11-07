<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;

/**
 * Test Case
 */
abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    use CreatesApplication;

    /**
     * Storage
     *
     * @var Illuminate\Support\Facades\Storage
     */
    protected $storage;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->storage = Storage::disk('local');
    }
}
