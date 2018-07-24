<?php
/**
 * @copyright 2018 Creative Cow Limited
 */
declare(strict_types=1);

namespace Cc\Stream;

interface SharedSocket
{
    const PARENT = 0;
    const CHILD = 1;
}