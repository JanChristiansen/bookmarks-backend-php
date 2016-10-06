<?php

namespace AppBundle\Tests;

use Doctrine\ORM\AbstractQuery;

class DoctrineQueryStub extends AbstractQuery
{
    public function setParameters($parameters)
    {
    }

    public function setFirstResult($firstResult)
    {
    }

    public function setMaxResults($maxResults)
    {
    }

    public function getResult($hydrationMode = self::HYDRATE_OBJECT)
    {
    }

    public function getSingleScalarResult()
    {
    }

    public function getScalarResult()
    {
    }

    public function getSQL()
    {
    }

    protected function _doExecute()
    {
    }
}