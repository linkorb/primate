<?php

namespace Primate;

use Primate\RepositoryInterface;
use Primate\Resource;
use Primate\Primate;
use RuntimeException;

class ArrayRepository implements RepositoryInterface
{
    protected $rows = [];
    
    public function __construct($rows)
    {
        $this->rows = $rows;
    }
    
    public function loadResourceCollection(Collection $collection, Primate $primate)
    {
        foreach ($this->rows as $key => $row) {
            $resource = new Resource($collection->getType(), $key);
            $collection->addResource($resource);
        }
    }
    
    public function loadResources($resources, Primate $primate)
    {
        foreach ($resources as $resource) {
            if (!$this->rows[$resource->getId()]) {
                throw new RuntimeException("No such row id: " . $resource->getId());
            }
            $row = $this->rows[$resource->getId()];
            foreach ($row as $key => $value) {
                if ($value[0]=='@') {
                    $part = explode(':', substr($value, 1));
                    $type = $primate->getType($part[0]);
                    $subResource = new Resource($type, $part[1]);
                    $value = $subResource;
                }
                $resource->setProperty($key, $value);
            }
        }
    }
}
