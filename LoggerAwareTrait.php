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
 * Trait for logger-aware Symfony services.
 *
 * @author Martin Jansen <martin@divbyzero.net>
 */
trait LoggerAwareTrait
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Injects the logger service into classes using this trait
     *
     * You are not expected to call this method directly. The Symfony2
     * compiler will take care of that.
     *
     * @param LoggerInterface $logger
     * @return LoggerAwareTrait Returns $this
     */ 
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Returns the logger service injected into classes using this trait
     *
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->logger;
    }
}
