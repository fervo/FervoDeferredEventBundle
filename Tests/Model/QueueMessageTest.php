<?php


namespace Fervo\DeferredEventBundle\Tests\Model;

use Fervo\DeferredEventBundle\Model\QueueMessage;

/**
 * Class QueueMessageTest
 *
 * @author Tobias Nyholm
 *
 */
class QueueMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testParseRawData()
    {
        $data="type: fish\nbaz: foo:bar\nserver: nginx\n\ntest content\n";
        $model = new QueueMessage();
        $model->parseRawData($data);

        $this->assertEquals('fish', $model->getHeader('type'));
        $this->assertEquals('foo:bar', $model->getHeader('baz'));
        $this->assertEquals('nginx', $model->getHeader('server'));
    }
} 