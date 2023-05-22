<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Config;

enum Format
{
    case Yml;
    case Yaml;
    case Json;
    case YmlToJson;

    public function isYml(): bool
    {
        return $this === self::Yml || $this === self::Yaml;
    }
}
