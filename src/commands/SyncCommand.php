<?php

namespace unclead\phpcluster\commands;

use GuzzleHttp\Client;

/**
 * Class SyncCommand
 * @package unclead\phpcluster\commands
 */
class SyncCommand extends BaseCommand
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $count;

    /**
     * @var string
     */
    private $partnerBaseUrl;

    private $errorMessage;

    public function __construct($id, $count, $partnerBaseUrl)
    {
        $this->id = $id;
        $this->count = $count;

        // todo add validation of partner base url
        $this->partnerBaseUrl = $partnerBaseUrl;
    }

    public function execute()
    {
        try {
            $client = new Client();
            $url = $this->partnerBaseUrl . '/cmd' . $this->id;
            $res = $client->request('POST', $url, [
                'form_params' => [
                    'count' => $this->count
                ]
            ]);
            return $res->getStatusCode() == 200;
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }

    }

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

}