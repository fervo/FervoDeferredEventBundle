# FervoDeferredEventBundle

This bundle allows you to run asynchronous background processes in PHP. You don't need a PHP-daemon nor do you have to
create a new php process for each job (which is expensive). We use PHP-FPM because it solves the performance and stability
issues for us.

I wrote [a blog entry](http://joiedetech.se/2013-11-25-improving-symfony-workers) with the reasoning behind this bundle,
which you may want to read.

## Usage

You can defer events in two ways. You can decide when you dispatch an event that it should be deferred or you could let
some listeners to decide if they should be deferred or not. In both cases we dispatch the event and put the job on
a message queue.

As if by magic, at some later time, a worker will dispatch your event to your listeners. Pretty much the only caveats
you'll need to keep in mind is that it is in another process, and that the code isn't executing in the request scope anymore.


### Let the listeners decide
Just tag your listener with ```fervo_deferred_event.listener``` instead of ```kernel.event_listener```,
and the bundle will do the rest. Simple as pie.

### Let the publisher decide

If you want all listeners to a event to be executed in a different thread you must wrap your Event in a DeferEvent.
Then use the event dispatcher and dispatch 'fervo.defer'.

```php
$event = new DeferEvent('foo.action', new MyEvent());
$this->get('event_dispatcher')->dispatch('fervo.defer', $event);
```

## Setup


### Server software

You need to install a message queue and a worker. The worker will pull jobs from the message queue and initiate the execution.

Currently we do support all message queues that support [AMQP](http://en.wikipedia.org/wiki/Advanced_Message_Queuing_Protocol)
and Sidekiq.

Here is a list of workers:

 * [fervo/deferred-event-worker](https://github.com/fervo/deferred-event-worker) written in Ruby.
 * [HappyR/DeferredEventJavaWorker](https://github.com/HappyR/DeferredEventJavaWorker) written in Java.


### Symfony setup
Add the bundle to your composer file, as well as well as your AppKernel.
Configure the bundle as follows:

```
# Sidekiq example
fervo_deferred_event:
    backend:
        type: sidekiq
        sidekiq_client_service: sidekiq_client

# Java/AMQP example:
fervo_deferred_event:
    backend:
        type: amqp
        amqp_config:
            host: "localhost"             #default
            port: 5672                    #default
        message_headers:
            fastcgi_host: "localhost"     #default
            fastcgi_port: 9000            #default
```

You do also need to setup one of the following (depending on you backend type):

 * [musicglue/sidekiq-job-pusher](https://github.com/musicglue/sidekiq-job-pusher/). Set up a client service, and pass it into the configuration.
 * [videlalvaro/php-amqplib](https://github.com/videlalvaro/php-amqplib/). Just add it to your composer.json.


## Error handling

The Java worker we take care of errors. When a worker unexpectedly terminates we will save the message and the error
 message on a separate queue. You may subscribe to that queue to log the error and to retry executing the job.