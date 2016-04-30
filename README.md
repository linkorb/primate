# Primate: The API ToolKit

<img src="http://www.earthtouchnews.com/media/385836/screen-shot-2014-08-21-at-101244-am-640x834_GalleryLarge.png"  style="width: 100%" />

Primate is a library that helps creating beautiful REST APIs.

It is heavily inspired by [Stormpath's "The Fundamentals of REST API Design"](https://stormpath.com/blog/fundamentals-rest-api-design/)


## Using Primate

First you'll need to instantiate Primate, before your app can serve requests:

```php
use Primate\Primate;

$primate = new Primate();
$primate->setBaseUrl('http://primate.example.com/api/v1');
$primate->setProperty('tenant', 'joe');
$primate->setProperty('x', 'y');
```

You'll notice that the BaseUrl of your API is set on the Primate instance, in order to output correct urls.

Additionally, we're registering some arbitrary properties to define the context of the requests.
You can use these properties later in order to fetch resources.

## Resources and Collections

In Primate APIs, a client can work with Resources and Collections.

* A **Resource** is simply an "object" in your application. For example, a User, a Product, etc.
* A **Collection** is simply an array of **Resources**.

## Types

Each **Resource** is of a specified **Type**.

In Primate, you'll need to register one or more Types before you can use them. For example:

```php
$repo = new MyContactRepository();
$type = new Type('contacts', $repo);
$primate->registerType($type);
```

Each Type has a name and a repository. The Repository can be any class that's implementing the `Primate\RepositoryInterface`.

It's recommended to take your existing application repositories, and make them implement this interface.

For example:

```php
<?php

namespace Example;

use Primate\RepositoryInterface;
use Primate\Resource;
use Primate\Primate;
use RuntimeException;

class PdoContactRepository implements RepositoryInterface
{
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    
    // Implement your regular repository methods here...
    
    public function loadResourceCollection(Collection $collection, Primate $primate)
    {
        foreach ($this->getAllContacts() as $contact) {
            $resource = new Resource($collection->getType(), $contact->getId());
            $collection->addResource($resource);
        }
    }
    
    public function loadResources($resources, Primate $primate)
    {
        foreach ($resources as $resource) {
            $contact = $this->findById($resource->getId());
            $resource->setProperty('name', $contact->getName());
            $resource->setProperty('gender', $contact->getGender());
            $phoneResource = $primate->createResource('phones', $contact->getPhoneId());
            $resource->setProperty('phone', $phoneResource);
        }
    }
}
```

Primate will call these methods to load resource data.

## Calling Primate

You can make requests to Primate like this:

```php
$data = $primate->getDataByPath($path, $expands);
echo json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
```

The `path` variable is either:

* `/{typeName}`: This will return a collection of all resources of that type
* `/{typeName}/{resourceId}`: This will return the specific resource


Additionally, you can pass an array of `expands`.

## Sub-resource expansion

When passing keys in the expands parameter, Primate will automatically expand the specified sub-resources.

For example, if your first response looks like this:

```json
{
    "href": "http://primate.example.com/api/v1/contacts/1",
    "name": "Alice",
    "gender": "Female",
    "phone": {
        "href": "http://primate.example.com/api/v1/phones/xyz"
    }
}
```

You can ask Primate to 'expand the `phone` property:

```json
{
    "href": "http://primate.example.com/api/v1/contacts/1",
    "name": "Alice",
    "gender": "Female",
    "phone": {
        "href": "http://primate.example.com/api/v1/phones/xyz",
        "number": "+1 987654321",
        "type": "mobile"
    }
}
```


## License

MIT (see [LICENSE.md](LICENSE.md))

## Brought to you by the LinkORB Engineering team

<img src="http://www.linkorb.com/d/meta/tier1/images/linkorbengineering-logo.png" width="200px" /><br />
Check out our other projects at [linkorb.com/engineering](http://www.linkorb.com/engineering).

Btw, we're hiring!
