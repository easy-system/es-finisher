<?php
/**
 * This file is part of the "Easy System" package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author  Damon Smith <damon.easy.system@gmail.com>
 */
namespace Es\Finisher\Test\Listener;

use Es\Finisher\Listener\ResponseListener;
use Es\Http\Response;
use Es\Http\Response\SapiEmitter;
use Es\Server\Server;
use Es\System\SystemEvent;

class ResponseListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testInvokeOnSuccess()
    {
        $response = new Response();
        $event    = new SystemEvent();
        $event->setResult(SystemEvent::FINISH, $response);

        $server  = new Server();
        $emitter = $this->getMock(SapiEmitter::CLASS);
        $server->setEmitter($emitter);
        $emitter
            ->expects($this->once())
            ->method('emit')
            ->with($this->identicalTo($response));

        $listener = new ResponseListener();
        $listener->setServer($server);

        $listener($event);
    }

    public function invalidResponseDataProvider()
    {
        return [
            [null],
            [true],
            [false],
            [100],
            ['string'],
            [[]],
            [new \stdClass()],
        ];
    }

    /**
     * @dataProvider invalidResponseDataProvider
     */
    public function testInvokeRaiseExceptionIfEventResultIsNotPsrResponse($response)
    {
        $event = new SystemEvent();
        $event->setResult(SystemEvent::FINISH, $response);

        $listener = new ResponseListener();
        $this->setExpectedException('UnexpectedValueException');
        $listener($event);
    }
}
