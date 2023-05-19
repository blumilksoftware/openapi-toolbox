<?php

declare(strict_types=1);

namespace Blumilk\OpenApiToolbox\Config;

use Illuminate\Contracts\Config\Repository;

class ConfigHelper
{
    public function __construct(
        protected Repository $config,
    ) {}

    public function getPath(string $file): string
    {
        $path = $this->config->get("openapi_toolbox.directory.path");

        return "$path/$file";
    }

    public function getIndex(): string
    {
        return $this->getPath($this->config->get("openapi_toolbox.directory.index"));
    }
}
