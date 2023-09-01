<?php

namespace App\Models;

trait SerializesDates
{
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('c');
    }
}
