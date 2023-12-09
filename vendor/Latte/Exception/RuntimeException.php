<?php

/**
 * This file is part of the PandaCMS (https://pandacms.net)
 * Copyright (c) 2023 Yumin Gui (https://yumindev.com)
 */

declare(strict_types=1);

namespace Latte\Exception;

use Latte\Exception\IException;

class RuntimeException extends \RuntimeException implements IException
{
}
