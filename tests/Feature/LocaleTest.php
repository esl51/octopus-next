<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleTest extends TestCase
{
    /** @test */
    public function can_set_locale_from_header()
    {
        $this->withHeaders(['Accept-Language' => 'ru'])
            ->postJson('/login');

        $this->assertEquals('ru', $this->app->getLocale());
    }
}
