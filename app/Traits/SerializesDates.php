<?php

namespace App\Traits;

trait SerializesDates
{
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('c');
    }
}
