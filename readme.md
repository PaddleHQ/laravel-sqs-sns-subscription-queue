# AWS SQS SNS Subscription Queue

Forked from https://github.com/joblocal/laravel-sqs-sns-subscription-queue for compatability with Laravel 5.2.

A simple extension to the [Illuminate/Queue](https://github.com/illuminate/queue) queue system used in [Laravel](https://laravel.com) and [Lumen](https://lumen.laravel.com/).

Using this connector allows [SQS](https://aws.amazon.com/sqs/) messages originating from a [SNS](https://aws.amazon.com/sns/) subscription to be worked on with Illuminate\Queue\Jobs\SqsJob.

This is especially useful in a miroservice architecture where multiple services subscribe to a common topic with their queues.

Understand that this package will not handle publishing to SNS, please use the [AWS SDK](https://aws.amazon.com/sdk-for-php/) to publish an event to SNS.


## Requirements

-   Laravel (tested with version 5.2)
-   or Lumen (tested with version 5.2)


## Usage

Add the LaravelSqsSnsSubscriptionQueue ServiceProvider to your application.


### Laravel
[Registering Service Providers in Laravel](https://laravel.com/docs/5.6/providers#registering-providers)
```php
'providers' => [
    // ...
    Joblocal\LaravelSqsSnsSubscriptionQueue\SqsSnsServiceProvider::class,
],
```

### Lumen
[Registering Service Providers in Lumen](https://lumen.laravel.com/docs/5.6/providers#registering-providers)
```php
$app->register(Joblocal\LaravelSqsSnsSubscriptionQueue\SqsSnsServiceProvider::class);
```


### Configuration

You'll need to configure the queue connection in your config/queue.php

AWS config found in config/aws.php will be automatically merged in.

```php
'connections' => [
  'sqs-sns-connection' => [
    'driver' => 'sqs-sns',
    'queue'  => env('QUEUE_URL', 'your-queue-url'),
    'routes' => [
        // you can use the "Subject" field
        'Subject' => 'App\\Jobs\\YourJob',
        // or the "TopicArn" of your SQS message
        'TopicArn:123' => 'App\\Jobs\\YourJob',
        // to specify which job class should handle the job
    ],
  ],
],
```

Once the sqs-sns queue connector is configured you can start
using it by running `php artisan queue:listen sqs-sns-connection`

Note that the body of SNS messages must be valid JSON for this queue processor to work.


### Job class example

```php
namespace App\Jobs;

use Joblocal\LaravelSqsSnsSubscriptionQueue\BaseJob;

class YourJob extends BaseJob
{
    public function handle()
    {
        // handle queue item
        var_dump($this->subject, $this->payload);
    }
}
```

## Message transformation

When SNS publishes to SQS queues the received message signature is as follows:

```json
{
  "Type" : "Notification",
  "MessageId" : "63a3f6b6-d533-4a47-aef9-fcf5cf758c76",
  "TopicArn" : "arn:aws:sns:us-west-2:123456789012:MyTopic",
  "Subject" : "Testing publish to subscribed queues",
  "Message" : "Hello world!",
  "Timestamp" : "2017-03-29T05:12:16.901Z",
  "SignatureVersion" : "1",
  "Signature" : "...",
  "SigningCertURL" : "...",
  "UnsubscribeURL" : "..."
} 
```

Illuminate\Queue\Jobs\SqsJob requires the following signature:

```json
{
  "job": "Illuminate\\Queue\\CallQueuedHandler@call",
  "data": {
    "commandName": "App\\Jobs\\YourJob",
    "command": "...",
  }
}
```
