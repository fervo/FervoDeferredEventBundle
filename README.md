# FervoDeferredEventBundle

I wrote [a blog entry](http://joiedetech.se/2013-11-25-improving-symfony-workers) with the reasoning behind this bundle, which you may want to read.

FervoDeferredEventBundle allows you to defer execution of events. Just tag your listener with ```fervo_deferred_event.listener``` instead of ```kernel.event_listener``` (subscribers don't work yet), and the bundle will do the rest.

As if by magic, at some later time, a worker will dispatch your event to your listeners. Pretty much the only caveats you'll need to keep in mind is that it is in another process, and that the code isn't executing in the request scope anymore.

Of course FervoDeferredEventBundle requires some kind of a message queue and a worker. Currently it uses Sidekiq, but the bundle itself is fairly backend agnostic, and should be easily portable to other MQs. The Worker code is available in the [fervo/deferred-event-worker](https://github.com/fervo/deferred-event-worker) repository.

## Setup

Add the bundle to your composer file, as well as well as your AppKernel. The bundle uses [musicglue/sidekiq-job-pusher](https://github.com/musicglue/sidekiq-job-pusher/), so set up a client service, and pass it into the configuration.

Configure the bundle as follows:

```
fervo_deferred_event:
    backend:
        type: sidekiq
        sidekiq_client_service: sidekiq_client
```

You'll also need to set up Sidekiq. Grab the [fervo/deferred-event-worker](https://github.com/fervo/deferred-event-worker) repository and follow the instructions there.
