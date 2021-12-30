<?php

namespace Fduarte42\StaticFiles;


use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class ConfigProvider
{
    #[ArrayShape(['dependencies' => "\string[][]", 'middleware_pipeline' => "array[]"])]
    #[Pure]
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'middleware_pipeline' => $this->getMiddlewarePipeline(),
        ];
    }


    #[ArrayShape(['factories' => "string[]"])]
    public function getDependencies(): array
    {
        return [
            'factories' => [
                'static-files-middleware-pipe' => StaticFilesMiddlewarePipeFactory::class,
            ],
        ];
    }


    public function getMiddlewarePipeline(): array
    {
        return [
            [
                'middleware' => 'static-files-middleware-pipe',
                'priority' => 1000,
            ],
        ];
    }


}
