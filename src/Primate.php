<?php

namespace Primate;

class Primate
{
    protected $baseUrl;
    protected $types = [];
    
    public function registerType(Type $type)
    {
        $this->types[$type->getName()] = $type;
    }

    public function getTypes()
    {
        return $this->types;
    }
    
    public function hasType($name)
    {
        return isset($this->types[$name]);
    }
    
    public function getType($name)
    {
        if (!$name) {
            throw new PrimateException("Calling getType requires a name");
        }
        if (!$this->hasType($name)) {
            throw new PrimateException("No such type registered: " . $name);
        }
        return $this->types[$name];
    }
    
    protected $properties = [];
    
    public function hasProperty($name)
    {
        return isset($this->properties[$name]);
    }
    
    public function getProperty($name)
    {
        if (!$this->hasProperty($name)) {
            throw new PrimateException("No such property: " . $name);
        }
        return $this->properties[$name];
    }
    
    public function setProperty($name, $value)
    {
        $this->properties[$name] = $value;
        return $this;
    }
    
    
    public function loadResource(Resource $resource, $expands = [])
    {
        $repo = $resource->getType()->getRepository();
        $resources = [$resource];
        $repo->loadResources($resources, $this);
        foreach ($resource->getProperties() as $key => $value) {
            if (in_array($key, $expands)) {
                foreach ($expands as $expand) {
                    $subExpands = [];
                    if (substr($expand, 0, strlen($key)+1) == $key . '.') {
                        $subExpands[] = substr($expand, strlen($key) + 1);
                    }
                }
                $subResource = $resource->getProperty($key);
                $this->loadResource($subResource, $subExpands);
            }
        }
        return $resource;
    }
    
    public function getResourceCollection($typeName)
    {
        $type = $this->getType($typeName);
        $collection = new Collection($type);
        $repo = $type->getRepository();
        $repo->loadResourceCollection($collection, $this);
        //print_r($resource);
        return $collection;
    }

    
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
    
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }
    
    public function getDataByPath($path, $expands)
    {
        $part = explode('/', $path);

        if ($part[0]!='') {
            throw new InvalidArgument("URI should start with a slash: " . $path);
        }
        
        $serializer = new Serializer($this);

        $typeName = $part[1];
        $type = $this->getType($typeName);
        if (isset($part[2])) {
            $resourceId = $part[2];
            
            $resource = new Resource($type, $resourceId);

            $resource = $this->loadResource($resource, $expands);
            $data = $serializer->serializeResource($resource);
            return $data;
        } else {
            $collection = $this->getResourceCollection($typeName);
            $data = $serializer->serializeCollection($collection);
            return $data;
        }
    }
    
}
