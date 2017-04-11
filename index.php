<?php
include_once './vendor/autoload.php';
echo '<pre>';

$hosts  = [
    'host' => '192.168.1.10',
    'port' => '9200'
];
$client = \Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build();

$kw = $_GET['kw'];

$searchParams['index'] = 'my_index';
$searchParams['type'] = 'my_type';
$searchParams['body']['query']['match']['title'] = $kw;

$result = $client->search($searchParams);

var_dump($result);