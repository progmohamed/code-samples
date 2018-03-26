<?php

namespace AdminBundle\Classes;

use Doctrine\Common\DataFixtures\FixtureInterface;

abstract class AbstractDataFixturesService
{
    protected $fixtures = [];

    public function getDataFixtures()
    {
        return $this->fixtures;
    }

    public function addDataFixture(FixtureInterface $menu)
    {
        $this->fixtures[] = $menu;
        return $this;
    }
}