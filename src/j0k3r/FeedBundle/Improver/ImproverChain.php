<?php

namespace j0k3r\FeedBundle\Improver;

class ImproverChain
{
    private $improvers;

    public function __construct()
    {
        $this->improvers = array();
    }

    /**
     * Add an improver to the chain
     *
     * @param Nothing $improver
     * @param string  $alias
     */
    public function addImprover(Nothing $improver, $alias)
    {
        $this->improvers[$alias] = $improver;
    }

    /**
     * Loop thru all improver and return one that match
     *
     * @param string $host A host
     *
     * @return Nothing|false
     */
    public function match($host)
    {
        foreach ($this->improvers as $alias => $improver) {
            if (true === $improver->match($host)) {
                return $improver;
            }
        }

        return false;
    }
}