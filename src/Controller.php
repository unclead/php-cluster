<?php

namespace PhpCluster;

use PhpCluster\Command\UpdateCountCommand;
use PhpCluster\Command\IncrementCountCommand;
use PhpCluster\Command\SyncCommand;

/**
 * Class Controller
 */
class Controller
{
    /**
     * @var Application
     */
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * Perfomr increment action.
     *
     * @param $id
     * @return int
     */
    public function actionIncrement($id)
    {
        $entity = $this->findEntity($id);

        $command = new IncrementCountCommand($entity);
        $content = $command->execute();

        $this->context->getLogger()->info('IncrementCountCommand completed successfully. Result: ' . $content);

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
        $command = new UpdateCountCommand($entity, $data['count']);
        $command->execute();

        $this->context->getLogger()->info('UpdateCountCommand completed successfully.');
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
            $this->context->getLogger()->debug('Cmd instance for ID: ' . $id . ' not found. Perform initialization.');
            $cmd = new Cmd();
            $collection->set($id, $cmd);
        }
        return $collection->get($id);
    }
}