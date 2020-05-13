<?php

namespace Joblocal\LaravelSqsSnsSubscriptionQueue\Queue\Connectors;

use Aws\Sqs\SqsClient;
use Illuminate\Queue\Connectors\SqsConnector;
use Illuminate\Support\Arr;

use Joblocal\LaravelSqsSnsSubscriptionQueue\Queue\SqsSnsQueue;

class SqsSnsConnector extends SqsConnector
{
    /**
     * @var array
     */
    protected $awsConfig;

    /**
     * SqsSnsConnector constructor.
     *
     * @param array $awsConfig
     */
    public function __construct(array $awsConfig = [])
    {
        $this->awsConfig = $awsConfig;
    }

    /**
     * Establish a queue connection.
     *
     * @param array $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        $config = $this->getGlobalConfig($config);

        return new SqsSnsQueue(
            new SqsClient($config),
            $config['queue'],
            Arr::get($config, 'prefix', ''),
            Arr::get($config, 'routes', [])
        );
    }

    protected function getGlobalConfig(array $config): array
    {
        return $this->getDefaultConfiguration(array_merge($this->awsConfig, $config));
    }
}
