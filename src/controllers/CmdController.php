<?php

namespace unclead\phpcluster\controllers;

use unclead\phpcluster\commands\UpdateCommand;
use unclead\phpcluster\models\Cmd;
use unclead\phpcluster\commands\IncrementCommand;
use unclead\phpcluster\commands\SyncCommand;

/**
 * Class CmdController
 * @package unclead\phpcluster\controllers
 */
class CmdController extends BaseController
{
    /**
     * Perfomr increment action.
     *
     * @param $id
     * @return int
     */
    public function actionIncrement($id)
    {
        $entity = $this->findEntity($id);

        $command = new IncrementCommand($entity);
        $content = $command->execute();

        $config = $this->context->getConfig();
        foreach ($config->getPartnerPorts() as $port) {
            $partnerBaseUrl = 'http://' . $config->getHost() . ':' . $port;
            $command = new SyncCommand($id, $entity->getCount(), $partnerBaseUrl);
            if ($command->execute()) {
                $this->context->getLogger()->info('Synchronization with instance ' . $partnerBaseUrl . ' completed successfully.');
            } else {
                $this->context->getLogger()->error('An error occurred during synchronization with instance:' . $partnerBaseUrl);
                $this->context->getLogger()->error('Detail: ' . $command->getErrorMessage());
            }
        }

        return $content;
    }


    /**
     * Update current state of entity.
     *
     * @param $id
     * @param $data
     * @return bool|int
     */
    public function actionUpdate($id, $data)
    {
        if (!isset($data['count'])) {
            $this->context->getLogger()->error('Invalid data for update');
            return 0;
        }

        $entity = $this->findEntity($id);
        $command = new UpdateCommand($entity, $data['count']);
        $command->execute();

        return true;
    }

    /**
     * @return string
     */
    public function actionSummary()
    {
        $collection = $this->context->getCmdCollection();

        $content = [];
        foreach ($collection->all() as $id => $entity) {
            $content[] = [
                'id' => $id,
                'count' => $entity->getCount()
            ];
        }
        return json_encode($content);
    }

    /**
     * Return entity instance.
     *
     * @param $id
     * @return null|Cmd
     */
    private function findEntity($id)
    {
        $collection = $this->context->getCmdCollection();

        if (!$collection->has($id)) {
            $this->context->getLogger()->debug('Command instance for ID: ' . $id . ' is not initialized. Perform initialization.');
            $cmd = new Cmd();
            $collection->set($id, $cmd);
        }
        return $collection->get($id);
    }
}