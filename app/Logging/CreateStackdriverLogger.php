<?php

namespace App\Logging;
use Google\Cloud\Logging\LoggingClient;
use Monolog\Handler\PsrHandler;
use Monolog\Logger;
class CreateStackdriverLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        $logName = isset($config['logName']) ? $config['logName'] : 'app';
        $options = [
            'resource' => [
                'type' => 'gce_instance',
                'labels' => [
                    'zone' => env('GCE_ZONE'),
                    'instance_id' => env('GCE_INSTANCE_ID')
                ]
            ]
        ];
        $psrLogger = LoggingClient::psrBatchLogger($logName, $options);
        $handler = new PsrHandler($psrLogger);
        $logger = new Logger($logName, [$handler]);
        return $logger;
    }
}