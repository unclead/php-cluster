<?php

namespace PhpCluster;

/**
 * Class CmdCollection
 */
class CmdCollection
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Strore command in registry.
     *
     * @param string $key
     * @param Cmd  $model
     *
     * @return void
     */
    public function set($key, Cmd $model)
    {
        $this->data[$key] = $model;
    }
    /**
     * Returns entity with the specified key.
     *
     * @param string $key
     *
     * @return Cmd|null
     */
    public function get($key)
    {
        return $this->data[$key];
    }

    /**
     * Whether entity with the specified key exists.
     *
     * @param int $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * @return Cmd[]
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->data) == 0;
    }

    public function __toString()
    {
        $result = [];
        foreach ($this->all() as $id => $entity) {
            $result[$id] = $entity->getCount();
        }
        return json_encode($result);
    }
}