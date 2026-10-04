<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * A refund request was refused for a business reason. The message is safe to
 * show to the agent.
 */
class RefundRequestBlocked extends RuntimeException {}
