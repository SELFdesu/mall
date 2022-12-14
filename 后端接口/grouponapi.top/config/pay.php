<?php

declare(strict_types=1);

use Yansongda\Pay\Pay;

return [
    'alipay' => [
        'default' => [
            // 支付宝分配的 app_id
            'app_id' => env('APP_ID',''),
            // 应用私钥
            'app_secret_cert' => env('APP_SECRET_CERT',''),
            // 应用公钥证书 路径
            'app_public_cert_path' => env('APP_PUBLIC_CERT_PATH',''),
            // 支付宝公钥证书 路径
            'alipay_public_cert_path' => env('ALIPAY_PUBLIC_CERT_PATH',''),
            // 支付宝根证书 路径
            'alipay_root_cert_path' => env('ALIPAY_ROOT_CERT_PATH',''),
            'return_url' => '',
            'notify_url' => env('NOTIFY_URL'),
            'mode' => Pay::MODE_SANDBOX,
        ],
    ],
    'wechat' => [
        'default' => [
            // 公众号 的 app_id
            'mp_app_id' => '',
            // 小程序 的 app_id
            'mini_app_id' => '',
            // app 的 app_id
            'app_id' => '',
            // 商户号
            'mch_id' => '',
            // 合单 app_id
            'combine_app_id' => '',
            // 合单商户号
            'combine_mch_id' => '',
            // 商户秘钥
            'mch_secret_key' => '',
            // 商户私钥
            'mch_secret_cert' => '',
            // 商户公钥证书路径
            'mch_public_cert_path' => '',
            // 微信公钥证书路径
            'wechat_public_cert_path' => [
                '' => '',
            ],
            'notify_url' => '',
            'mode' => Pay::MODE_NORMAL,
        ],
    ],
    'http' => [ // optional
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    // optional，默认 warning；日志路径为：sys_get_temp_dir().'/logs/yansongda.pay.log'
    'logger' => [
        'enable' => false,
        'file' => storage_path('logs/alipay.log'),
        'level' => 'debug',
        'type' => 'single', // optional, 可选 daily.
        'max_file' => 30,
    ],
];
