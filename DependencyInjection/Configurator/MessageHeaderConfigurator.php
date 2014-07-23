<?php

namespace Fervo\DeferredEventBundle\DependencyInjection\Configurator;

use Fervo\DeferredEventBundle\Service\MessageHeaderAwareInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class MessageHeaderConfigurator
 *
 * @author Tobias Nyholm
 */
class MessageHeaderConfigurator
{
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface kernel
     */
    private $kernel;

    /**
     * @param KernelInterface $kernel
     */
    function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param MessageHeaderAwareInterface $messageService
     */
    public function configure(MessageHeaderAwareInterface $messageService)
    {
        //try to set default php bin
        if ($messageService->getHeader('php_bin') === null) {
            if (defined(PHP_BINARY)) {
                //since php 5.4
                $messageService->setHeader('php_bin', PHP_BINARY);
            } else {
                $messageService->setHeader('php_bin', PHP_BINDIR.'/php');
            }
        }

        //try to set default app_console
        if ($messageService->getHeader('console_path') === null) {
            $messageService->setHeader('console_path', $this->kernel->getRootDir().'/console');
        }

        //try to set default dispatch_path
        if ($messageService->getHeader('dispatch_path') === null) {
            $messageService->setHeader(
                'dispatch_path',
                $this->kernel->locateResource('@FervoDeferredEventBundle/Resources/bin/dispatch.php')
            );
        }
    }
} 