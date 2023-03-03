<?php

class APIController extends BaseController{

    public function test(){
        $mErrorCode = 422;
        $mErrorDesc = '';
        $mReqMethod = $_SERVER['REQUEST_METHOD'];
        $mQueryParam = $this->getQueryStringParam();

        if(strtoupper($mReqMethod) === 'GET'){
            try{
                $asetModel = new AsetModel();

                $reqLimit = 10;
                if(isset($mQueryParam['limit']) && $mQueryParam['limit']){
                    $reqLimit = $mQueryParam['limit'];
                }

                $asetData = $asetModel->getAll($reqLimit);
            }catch(Error $err){
                $mErrorDesc = $err->getMessage().' Terjadi Kesalahan Server';
                $mErrorCode = 500;
            }
        }else{
            $mErrorDesc = 'Method tidak dikenal';
            $mErrorCode = 422;
        }

        if(!$mErrorDesc){
            $this->responseOK($asetData);
        }else{
            $this->responseErr($mErrorCode, $mErrorDesc);
        }
    }
}