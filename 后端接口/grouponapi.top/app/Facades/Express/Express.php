<?php
namespace App\Facades\Express;

use Illuminate\Support\Facades\Http;

class Express{

    //商户ID
    protected $EBusinessID;
    
    //API key
    protected $AppKey;

    //模式
    protected $mode;

    public function __construct(){
        $config=config('express');
        $this->EBusinessID=$config['EBusinessID'];
        $this->AppKey=$config['AppKey'];
        $this->mode=$config['mode']?? 'product';
    }

    //即时查询
    public function traces($ShipperCode,$LogisticCode){


        //请求参数
        // 组装应用级参数
        $requestData= "{'OrderCode': '','ShipperCode': '{$ShipperCode}','LogisticCode': '{$LogisticCode}'}";
        

        //发起请求
        $result=Http::asForm()->post(
            $this->url('traces'),
            $this->formatReqData($requestData,1002)
        );

        return $this->formatResData($result);


    }
    // 格式化请求参数
    protected function formatReqData($requestData,$RequestType){
        $datas = array(
            'EBusinessID' => $this->EBusinessID,
            'RequestType' => $RequestType, //快递查询接口指令8002/地图版快递查询接口指令8004
            'RequestData' => urlencode($requestData) ,
            'DataType' => '2',
        );
        $datas['DataSign'] = $this->encrypt($requestData);

        return $datas;
    }

    //返回api的url
    protected function url($type){
        $url=[
            'traces'=>[
                'product'=>'https://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx',
                'dev'=>'http://www.kdniao.com/UserCenter/v2/SandBox/SandboxHandler.ashx?action=CommonExcuteInterface',
            ]
        ];

        return $url[$type][$this->mode];
    }

    /**
     * 
     */
    protected function formatResData($result){
        $result=json_decode($result,true);

        //api服务器错误
        if($result['Success']==false){
            return $result;
        }
        $result=json_decode($result['ResponseData'],true);
        //请求成功但没有数据，参数有误
        if($result['Success']==false){
            return $result['Reason'];
        }
        return $result;
    }

    /**
     * 电商Sign签名生成
     * @param data 内容   
     * @param ApiKey ApiKey
     * @return DataSign签名
     */
    protected function encrypt($data) {
        return urlencode(base64_encode(md5($data.$this->AppKey)));
    }

}