<?php

namespace Fervo\DeferredEventBundle\Model;

/**
 * @author Tobias Nyholm
 *
 * This is a message that should be sent to the queue. The protocol that is being used is similar to HTTP
 */
class QueueMessage
{
    /**
     * @var array headers
     */
    protected $headers;

    /**
     * @var string data
     * The serialized event
     */
    protected $data;

    /**
     * @param null $data
     */
    public function __construct($data=null)
    {
        $this->headers=array();
        $this->data = $data;
    }

    public function __toString()
    {
        return $this->getFormattedMessage();
    }

    /**
     * Parse raw data from queue
     *
     * @param $raw
     */
    public function parseRawData($raw)
    {
        $this->clear();
        $lines=explode("\n", $raw);
        foreach ($lines as $i=>$line) {
            if ($line=='') {
                $this->data=$lines[$i+1];
                break;
            }
            list($key, $value) = explode(':',$line);
            $this->headers[strtolower($key)]=trim($value);
        }
    }

    /**
     * Clear message contents
     */
    public function clear()
    {
        $this->headers=array();
        $this->data=null;
    }

    /**
     * Return a HTTP like message
     */
    public function getFormattedMessage()
    {
        $message='';
        foreach ($this->headers as $name=>$value) {
            if (empty($value)) {
                continue;
            }

            $message.=sprintf("%s: %s\n", $name, $value);
        }
        $message.="\n".$this->data;

        return $message;
    }

    /**
     * @param mixed $name
     * @param mixed $value
     *
     * @return $this
     */
    public function addHeader($name, $value)
    {
        $this->headers[$name]=$value;

        return $this;
    }

    /**
     * @param $name
     *
     * @return string|null
     */
    public function getHeader($name)
    {
        if (empty($this->headers[$name])) {
            return null;
        }

        return $this->headers[$name];
    }


    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
