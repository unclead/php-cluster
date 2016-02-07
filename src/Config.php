<?php

namespace PhpCluster;

use PhpCluster\Exception\InvalidConfigurationException;

/**
 * Class Config
 * @package unclead\phpcluster
 */
class Config
{
    /**
     * @var int the port that listens to the application
     */
    private $port;

    /**
     * @var string the host (IP address) on which application is started.
     */
    private $host = '127.0.0.1';


    /**
     * @var int a list of ports of other applications in a cluster
     */
    private $partnerPorts = [];

    public function __construct($argv)
    {
        $this->init($argv);
    }

    /**
     * Internal initialization.
     *
     * @param $argv
     */
    private function init($argv)
    {
        $options = array_slice($argv, 1, count($argv) -1);

        foreach($options as $option) {
            preg_match('/^--([^=]+)=(.*)/', $option, $m);
            if (!isset($m[1])) {
                continue;
            }

            $name = $m[1];
            $value = $m[2];
            switch($name) {
                case 'port':
                    $this->port = $value;
                    break;
                case 'host':
                    $this->host = $value;
                    break;
                case 'partner-ports':
                    $ports = explode(',', $value);
                    array_walk($ports, function(&$val) {
                        $val = trim($val);
                    });
                    $ports = array_filter($ports);
                    $this->partnerPorts = $ports;
                    break;
                default:
                    break;
            }
        }
    }


    /**
     * @return int
     * @throws InvalidConfigurationException
     */
    public function getPort()
    {
        if (empty($this->port)) {
            throw new InvalidConfigurationException('You must specify port via option: --port');
        }
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return array
     */
    public function getPartnerPorts()
    {
        return $this->partnerPorts;
    }

    /**
     * @param int $partnerPorts
     */
    public function setPartnerPorts($partnerPorts)
    {
        $this->partnerPorts = $partnerPorts;
    }
}