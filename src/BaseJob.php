<?php

namespace Joblocal\LaravelSqsSnsSubscriptionQueue;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

abstract class BaseJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * SNS message Subject
     *
     * @var string
     */
    protected $subject;

    /**
     * SNS message json decoded body
     *
     * @var array
     */
    protected $payload;

    /**
     * @param string $subject SNS Subject
     * @param array  $payload JSON decoded 'Message'
     */
    public function __construct(string $subject, array $payload)
    {
        $this->subject = $subject;
        $this->payload = $payload;
    }

    /**
     * Process the job. To fail the job throw an exception.
     *
     * @throws \Exception
     * @return void
     */
    abstract public function handle();
}
