<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Storage;

/**
 * Test Case
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshTestDatabase;

    /**
     * Storage
     *
     * @var Illuminate\Support\Facades\Storage
     */
    protected $storage;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->storage = Storage::disk('local');
    }

    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();

        $this->refreshTestDatabase();

        return $uses;
    }
}
