<?php

namespace Primate;

class Serializer
{
    protected $primate;
    
    public function __construct(Primate $primate)
    {
        $this->primate = $primate;
    }
    
    public function serializeResource(Resource $resource)
    {
        $data['id'] = $resource->getId();
        $data['_type'] = $resource->getType()->getName();
        $data['href'] = $this->primate->getBaseUrl() . '/' . $resource->getType()->getName() . '/' . $resource->getId();
        foreach ($resource->getProperties() as $name => $value) {
            switch (gettype($value)) {
                case 'array':
                    $data[$name] = '[]';
                    break;
                case 'object':
                    if (is_subclass_of($value, '\Primate\ResourceInterface')) {
                        $data[$name] = $this->serializeResource($value);
                    } else {
                        $data[$name] = '???';
                    }
                    break;
                default:
                    $data[$name] = $value;
                    break;
            }
        }
        return $data;
    }
    
    public function serializeCollection(Collection $collection)
    {
        $data = [];
        $resources = $collection->getResources();
        
        $data['href'] = $this->primate->getBaseUrl() . '/' . $collection->getType()->getName();
        $data['offset'] = 0;
        $data['limit'] = 50;
        $data['count'] = count($resources);
        foreach ($resources as $resource) {
            $data['resources'][] = $this->serializeResource($resource);
        }
        
        return $data;
    }
}
