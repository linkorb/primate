<?php

namespace Primate;

use Primate\Primate;

interface RepositoryInterface
{
    public function loadResources($resources, Primate $primate);
}
