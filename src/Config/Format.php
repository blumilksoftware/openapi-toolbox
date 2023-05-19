<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Config;

enum Format: string
{
    case Yml = "yml";
    case Yaml = "yaml";
    case Json = "json";

    public function isYml(): bool
    {
        return $this === self::Yml || $this === self::Yaml;
    }
}
