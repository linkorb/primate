<?php

namespace Primate;

class Resource implements ResourceInterface
{
    
    public function __construct(Type $type, $id)
    {
        $this->setType($type);
        $this->setId($id);
    }
    
    protected $id;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
    
    protected $properties = [];
    
    public function getProperties()
    {
        return $this->properties;
    }
    
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
    }
    
    public function hasProperty($name)
    {
        return isset($this->properties[$name]);
    }
    
    public function getProperty($name)
    {
        if (!$this->hasProperty($name)) {
            throw new PrimateException("No such property name: " . $name);
        }
        return $this->properties[$name];
    }
}
