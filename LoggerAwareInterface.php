<?php
/**
 * This file is part of the LoggerAwareBundle.
 *
 * (c) Martin Jansen <martin@divbyzero.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace divbyzero\LoggerAwareBundle;

use Psr\Log\LoggerInterface;

/**
 * Describes a logger-aware service
 *
 * @author Martin Jansen <martin@divbyzero.net>
 */
interface LoggerAwareInterface
{
    public function setLogger(LoggerInterface $logger);
}
