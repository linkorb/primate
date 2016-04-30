<?php

namespace Primate;

class Type
{
    
    public function __construct($name, $repository = null)
    {
        $this->setName($name);
        if ($repository) {
            $this->setRepository($repository);
        }
    }
    private $name;
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    protected $repository;
    
    public function getRepository()
    {
        return $this->repository;
    }
    
    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
        return $this;
    }
}
