<?php
/**
 * @copyright 2018 Creative Cow Limited
 */
declare(strict_types=1);

namespace Cc\Stream;

use Zend\HttpHandlerRunner\Emitter\EmitterInterface;
use Zend\HttpHandlerRunner\Emitter\SapiStreamEmitter;

class ConfigProvider
{

    /**
     * Get configuration
     *
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'invokables' => [
                    EmitterInterface::class => SapiStreamEmitter::class,
                ],
                'factories'  => [
                    SharedSocket::class => SharedSocketFactory::class,
                ],
            ],
        ];
    }
}
