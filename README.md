# FervoDeferredEventBundle

I wrote a blog entry with the reasoning behind this bundle, which you may want to read.

FervoDeferredEventBundle allows you to defer execution of events, either by dispatching a wrapping event, or by calling a method on a service:

Events:

```
$evt = new DeferEvent('foo.action', new FooActionEvent());
$eventDispatcher->dispatch('fervo.defer', $evt);
```
Service:

```
$evt = new FooActionEvent();
$evt->setName('foo.action');
$container->get('fervo_dispatch.queue')->deferEvent($evt);
```

As if by magic, at some later time, a worker will dispatch your event into the event dispatcher. Pretty much the only caveats you'll need to keep in mind is that it is in another process, and that the code isn't executing in the request scope anymore.

Of course FervoDeferredEventBundle requires some kind of a message queue and a worker. Currently it uses Sidekiq, but the bundle itself is fairly backend agnostic, and should be easily portable to other MQs. The Worker code is available in the [fervo/deferred-event-worker](https://github.com/fervo/deferred-event-worker) repository.

## Setup

Add the bundle to your composer file, as well as well as your AppKernel. You probably also want to install [rdey/sidekiq-bundle](#) for easy access to a Sidekiq client.

Configure the bundle as follows:

```
fervo_deferred_event:
    backend:
        type: sidekiq
        sidekiq_client_service: redeye_sidekiq
```

You'll also need to set up Sidekiq. Grab the [fervo/deferred-event-worker](https://github.com/fervo/deferred-event-worker) repository and follow the instructions there.
