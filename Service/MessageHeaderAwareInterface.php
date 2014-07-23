<?php

namespace Fervo\DeferredEventBundle\Service;

/**
 * Class MessageHeaderAwareInterface
 *
 * @author Tobias Nyholm
 */
interface MessageHeaderAwareInterface 
{
    public function setHeader($name, $value);
    public function getHeader($name);
} 