<?php

namespace unclead\phpcluster\commands;

use GuzzleHttp\Client;
use unclead\phpcluster\collections\CmdCollection;
use unclead\phpcluster\Config;
use unclead\phpcluster\models\Cmd;

/**
 * Class PopulateCollectionCommand
 * @package unclead\phpcluster\commands
 */
class InitCollectionCommand extends BaseCommand
{
    /**
     * @var CmdCollection
     */
    private $collection;

    /**
     * @var Config
     */
    private $config;

    public function __construct($collection, $config)
    {
        $this->collection = $collection;
        $this->config = $config;
    }

    public function execute()
    {
        $summary = [];
        foreach ($this->config->getPartnerPorts() as $port) {
            $data = $this->performRequest($this->config->getHost(), $port);

            foreach($data as $row) {
                $id = $row['id'];
                $count = $row['count'];
                if (!isset($summary[$id])) {
                    $summary[$id] = $count;
                } elseif ($summary[$id] < $count) {
                    $summary[$id] = $count;
                }
            }
        }

        foreach ($summary as $id => $count) {
            $entity = new Cmd();
            $entity->setCount($count);
            $this->collection->set($id, $entity);
        }
    }

    private function performRequest($host, $port)
    {
        try {
            $partnerBaseUrl = 'http://' . $host . ':' . $port;
            $client = new Client();
            $url = $partnerBaseUrl . '/cmd';
            $res = $client->request('GET', $url);

            $result = [];
            if ($res->getStatusCode() == 200) {
                $result = json_decode((string) $res->getBody(), true);
            }

            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }
}