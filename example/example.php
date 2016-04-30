<?php

use Primate\Primate;
use Primate\Resource;
use Primate\Type;
use Primate\Serializer;
use Primate\ArrayRepository;
use Symfony\Component\Yaml\Parser;

require_once(__DIR__ . '/../vendor/autoload.php');

if (!isset($argv[1])) {
    echo "Usage: example.php /typeName/id\n";
    exit(1);
}
$url = $argv[1];
echo "Fetching $url\n";

$yaml = new Parser();
$data = $yaml->parse(file_get_contents(__DIR__ . '/example.yml'));

$primate = new Primate();
$primate->setBaseUrl('http://primate.example.com/api/v1');
$primate->setProperty('tennant', 'joe');

foreach ($data as $typeName => $rows) {
    $repo = new ArrayRepository($rows);
    $type = new Type($typeName, $repo);
    $primate->registerType($type);
}

$part = parse_url($url);
$path = $part['path'];
$query = $part['query'];
$expands=[];

$parts = explode('&', $query);
foreach ($parts as $part) {
    $kv = explode('=', $part);
    $key=$kv[0];
    $value=$kv[1];
    if ($key=='expand') {
        $expands = explode(',', $value);
    }
}
//print_r($part);


$data = $primate->getDataByPath($path, $expands);
echo json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);

echo "\n";
