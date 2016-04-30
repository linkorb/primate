<?php

namespace Primate;

class Collection
{
    protected $resources = [];
    
    public function __construct(Type $type)
    {
        $this->setType($type);
    }
    
    protected $type;
    
    public function getType()
    {
        return $this->type;
    }
    
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    
    public function addResource(ResourceInterface $resource)
    {
        $this->resources[] = $resource;
    }
    
    public function getResources()
    {
        return $this->resources;
    }
}
