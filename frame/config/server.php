<?php
return [
    "http" => [
        "host" => "0.0.0.0",
        "port" => 9001,
        "swoole" => [
            "task_worker_num" => 0,
        ],
        'ip' =>'127.0.0.1',
    ],
    'ws'=>[

        "host" => "0.0.0.0",               //服务监听ip`
        'port' => 9003,                      //监听端口
        'enable_http' => true,               //是否开启http服务
        'swoole' => [                        //swoole配置
            "task_worker_num" => 0,
            // 'daemonize' => 0,             //是否开启守护进程
        ],
        'ip' =>'127.0.0.1', //本机ip
        'is_handshake' => true,
    ],
    "rpc" => [
        'tcpable'=>1,                        //是否开启tcp监听
        "host" => "127.0.0.1",
        "port" => 9006,
        "swoole_setting" => [
            "worker_num" => "2"
        ]
    ],
    'route' => [
        'server' => [
            'ip' => '127.0.0.1',    //路由服务ip
            'port' => 9002,           //路由服务端口
        ],
        'jwt' => [
            'key' => 'swocloud',
            'alg' => [
                'HS256'
            ]
        ]
    ]
];
