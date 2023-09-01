<?php

namespace App\Models;

use Illuminate\Support\Facades\Schema;

trait HasColumns
{
    public static function getColumns(): array
    {
        $table = (new static())->getTable();
        $columns = [];
        foreach (Schema::getColumnListing($table) as $col) {
            $columns[] = $table . '.' . $col;
        }
        return $columns;
    }
}
