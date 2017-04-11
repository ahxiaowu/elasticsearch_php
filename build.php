<?php
include_once './vendor/autoload.php';
echo '<pre>';

$hosts  = [
    'host' => '122.200.94.55',
    'port' => '9200'
];
$client = \Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build();

/*$params = [
    'index' => 'my_index',
    'body' => [
        'settings' => [
            'number_of_shards' => 5,
            'number_of_replicas' => 0
        ],
        'mappings' => [
            'my_type' => [
                '_source' => [
                    'enabled' => true
                ],
                'properties' => [
                    'title' => [
                        'type' => 'string',
                        'analyzer' => 'chinese'
                    ],
                    'id' => [
                        'type' => 'integer'
                    ]
                ]
            ]
        ]
    ]
];
$response = $client->indices()->create($params);
var_dump($response);*/

$param['index'] = 'my_index';
$flag           = $client->indices()->exists($param);
#var_dump($flag);


// 开始建索引
/*
$data = http_post('https://wxappadmin.28.com/Api/Wxapp/prolist', ['client_id' => 5]);
$data = json_decode($data,true);
$ret = [];
foreach($data['data'] as $val){
    $ret[] = [
        'id' => $val['id'],
        'title' => $val['projectname']
    ];
}
file_put_contents('./data.txt',json_encode($ret,JSON_UNESCAPED_UNICODE));*/

$data = json_decode(file_get_contents('./data.txt'), true);
foreach ($data as $val) {
    $args          = [];
    $args['body']  = [
        'title' => $val['title'],
        'id'    => $val['id']
    ];
    $args['index'] = 'my_index';
    $args['type']  = 'my_type';
    $args['id']    = $val['id'];

    $client->index($args);
}


/**
 * POST 请求
 * @param string $url
 * @param array $param
 * @param boolean $post_file 是否文件上传
 * @return string content
 */
function http_post($url, $param, $post_file = false) {
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
    }
    if (is_string($param) || $post_file) {
        $strPOST = $param;
    } else {
        $aPOST = array();
        foreach ($param as $key => $val) {
            $aPOST[] = $key . "=" . urlencode($val);
        }
        $strPOST = join("&", $aPOST);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_TIMEOUT, 120);
    curl_setopt($oCurl, CURLOPT_POST, true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
    $sContent = curl_exec($oCurl);
    $aStatus  = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}