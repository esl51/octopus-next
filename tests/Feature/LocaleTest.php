<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    #[Test]
    public function can_set_locale_from_header()
    {
        $this->withHeaders(['Accept-Language' => 'ru'])
            ->postJson('/login');

        $this->assertEquals('ru', $this->app->getLocale());
    }
}
