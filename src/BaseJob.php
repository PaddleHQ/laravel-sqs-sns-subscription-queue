<?php

namespace Joblocal\LaravelSqsSnsSubscriptionQueue;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Joblocal\LaravelSqsSnsSubscriptionQueue\Exception\JsonDecodeException;

abstract class BaseJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * SNS Message
     *
     * @var array
     */
    protected $snsMessage;

    /**
     * @param array $snsMessage (
     *      @param string Type
     *      @param string MessageId
     *      @param string TopicArn
     *      @param string Subject
     *      @param string Message
     *      @param string Timestamp
     *      @param string SignatureVersion
     *      @param string Signature
     *      @param string SigningCertURL
     *      @param string UnsubscribeURL
     * )
     */
    public function __construct(array $snsMessage)
    {
        $this->snsMessage = $snsMessage;
    }

    /**
     * Process the job. To fail the job throw an exception.
     *
     * @throws \Exception
     * @return void
     */
    abstract public function handle();

    public function getType(): string
    {
        return $this->snsMessage['Type'] ?? '';
    }

    public function getMessageId(): string
    {
        return $this->snsMessage['MessageId'] ?? '';
    }

    public function getSubject(): string
    {
        return $this->snsMessage['Subject'] ?? '';
    }

    public function getMessage(): string
    {
        return $this->snsMessage['Message'] ?? '';
    }

    public function getTopicArn(): string
    {
        return $this->snsMessage['TopicArn'] ?? '';
    }

    public function getTimestamp(): string
    {
        return $this->snsMessage['Timestamp'] ?? '';
    }

    /**
     * Return a json decoded version of the SNS message
     *
     * @return array
     * @throws JsonDecodeException
     */
    public function getJsonDecodedMessage(): array
    {
        $message = json_decode($this->getMessage(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonDecodeException($this->getMessage(), json_last_error_msg());
        }

        return $message;
    }
}
