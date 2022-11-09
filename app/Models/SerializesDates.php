<?php

namespace App\Models;

trait SerializesDates
{
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('c');
    }
}
