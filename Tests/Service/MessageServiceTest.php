<?php

namespace Fervo\DeferredEventBundle\Tests\Service;

use Fervo\DeferredEventBundle\Service\MessageService;

/**
 * Class MessageServiceTest
 *
 * @author Tobias Nyholm
 */
class MessageServiceTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateMessage()
    {
        $format='json';
        $headers=array('foo'=>'bar','baz'=>'biz');
        $data='foobar';

        $event=$this->getMockBuilder('Symfony\Component\EventDispatcher\Event')
            ->disableOriginalConstructor()
            ->getMock();

        $serializer = $this->getMockBuilder('Symfony\Component\Serializer\SerializerInterface')
            ->setMethods(array('serialize'))
            ->disableOriginalConstructor()
            ->getMock();
        $serializer->expects($this->once())
            ->method('serialize')
            ->with($this->identicalTo($event), $this->identicalTo($format))
            ->willReturn($data);

        $service = new MessageService($headers, $serializer, $format);
        $message=$service->createMessage($event);

        //make sure we set the headers
        foreach ($headers as $name=>$value) {
            $this->assertEquals($value, $message->getHeader($name));
        }
    }
}