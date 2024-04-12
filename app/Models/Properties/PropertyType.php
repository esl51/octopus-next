<?php

namespace App\Models\Properties;

use App\Models\HasTypeEnum;

enum PropertyType: int
{
    use HasTypeEnum;

    case STRING = 1;
    case TEXT = 2;
    case HTML = 3;
    case INTEGER = 4;
    case FLOAT = 5;
    case BOOLEAN = 6;
    case SELECT = 7;
    case CHECKBOX = 8;
    case RADIO = 9;
    case FILE = 10;
    case IMAGE = 11;

    public function component()
    {
        return match ($this) {
            self::STRING => 'input',
            self::INTEGER => 'input',
            self::FLOAT => 'input',
            self::TEXT => 'textarea',
            self::HTML => 'editor',
            self::BOOLEAN => 'checkbox',
            self::SELECT => 'select',
            self::CHECKBOX => 'checkboxes',
            self::RADIO => 'radios',
            self::FILE => 'file',
            self::IMAGE => 'file'
        };
    }

    public function validator()
    {
        return match ($this) {
            self::STRING => 'string|max:255',
            self::INTEGER => 'integer',
            self::FLOAT => 'numeric',
            self::TEXT => 'string',
            self::HTML => 'string',
            self::BOOLEAN => 'boolean',
            self::SELECT => 'integer',
            self::CHECKBOX => 'integer',
            self::RADIO => 'integer',
            self::FILE => 'file',
            self::IMAGE => 'image'
        };
    }

    public function column()
    {
        return match ($this) {
            self::STRING => 'string_value',
            self::INTEGER => 'int_value',
            self::FLOAT => 'float_value',
            self::TEXT => 'text_value',
            self::HTML => 'text_value',
            self::BOOLEAN => 'int_value',
            self::SELECT => 'property_value_id',
            self::CHECKBOX => 'property_value_id',
            self::RADIO => 'property_value_id',
            self::FILE => null,
            self::IMAGE => null
        };
    }

    public function isMultiple()
    {
        return match ($this) {
            self::CHECKBOX, self::FILE => true,
            default => false
        };
    }

    public function hasValues()
    {
        return match ($this) {
            self::SELECT, self::CHECKBOX, self::RADIO => true,
            default => false
        };
    }

    public function hasFiles()
    {
        return match ($this) {
            self::FILE, self::IMAGE => true,
            default => false
        };
    }
}
