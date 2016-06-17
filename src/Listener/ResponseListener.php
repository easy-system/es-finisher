<?php
/**
 * This file is part of the "Easy System" package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author  Damon Smith <damon.easy.system@gmail.com>
 */
namespace Es\Finisher\Listener;

use Es\Server\ServerTrait;
use Es\System\SystemEvent;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;

/**
 * Sends a response to client.
 */
class ResponseListener
{
    use ServerTrait;

    /**
     * Sends a response to client.
     *
     * @param \Es\System\SystemEvent $event The system event
     *
     * @throws \UnexpectedValueException If the final result of a system event
     *                                   is not PSR Response
     */
    public function __invoke(SystemEvent $event)
    {
        $result = $event->getResult(SystemEvent::FINISH);
        if (! $result instanceof ResponseInterface) {
            throw new UnexpectedValueException(sprintf(
                'The system event provided invalid final result; must be '
                . 'an instance of "%s", "%s" received.',
                ResponseInterface::CLASS,
                is_object($result) ? get_class($result) : gettype($result)
            ));
        }

        $server  = $this->getServer();
        $emitter = $server->getEmitter();

        $emitter->emit($result);
    }
}
