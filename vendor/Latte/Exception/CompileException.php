<?php

/**
 * This file is part of the Panda Press (https://pandapress.org)
 * Copyright (c) 2024 Yumin Gui (https://yumin.io)
 */

declare(strict_types=1);

namespace Latte\Exception;

use Latte\Exception\IException;
use Latte\PositionAwareException;
use Latte\Compiler\Position;


class CompileException extends \Exception implements IException {
	use PositionAwareException;

	/** @deprecated */
	public ?int $sourceLine;


	public function __construct(string $message, ?Position $position = null, ?\Throwable $previous = null) {
		parent::__construct($message, 0, $previous);
		$this->position = $position;
		$this->sourceLine = $position?->line;
		$this->generateMessage();
	}
}
