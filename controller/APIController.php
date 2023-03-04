<?php

use Carbon\Carbon;

class APIController extends BaseController
{
    private $mTimeNow;

    public function __construct()
    {
        $this->mTimeNow = Carbon::now();
    }

    public function put_image()
    {
        /**
         * This will provide put image to server
         * 1. Image sent as param "image"
         * 2. The folder will be named based on account that make the request, example account kjanssdkjn have folder kjanssdkjn
         * 3. By that, will also have sub folder that named type of this request (image) 
         * 4. Then, filename will be have structure of YMDHISM
         * 5. Image saved in this module, and all image should be accessing by this module
         * 6. Future action will be provided multiple upload images.
         */

        header("Acess-Control-Allow-Origin: *");
        header("Acess-Control-Allow-Methods: POST");
        header("Acess-Control-Allow-Headers: Acess-Control-Allow-Headers,Content-Type,Acess-Control-Allow-Methods,Authorization");
        $this->methodAllowed("POST");

        # Check for active account
        //  $headers = getallheaders();
        //  $accountKey = $headers['Authorization'];

        $accountKey = $_POST['Authorization'];
        if ($accountKey == null) return $this->responseErr(401, "Anda tidak dikenal");
        $accData = $this->validateAccountKey($accountKey);

        try {

            # Extract image
            if (!isset($_FILES['image'])) throw new RuntimeException("Gambar tidak ada");

            $fileName = $_FILES['image']['name'];
            $tempPath = $_FILES['image']['tmp_name'];
            $fileSize = $_FILES['image']['size']; # karena penyimpanan server unlimited, jadi gausah dibatesin
            if (empty($fileName)) throw new RuntimeException("Gambar tidak ada, #2");
            $fileNameHash = sha1($fileName);
            $urlNameHash = sha1($this->mTimeNow);

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            if (false === $ext = array_search($finfo->file($_FILES['image']['tmp_name']), [
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'jpeg' => 'image/jpeg'
            ], true)) throw new RuntimeException("Format gambar tidak sesuai");

            $accPath = BASE_PATH . "\\assets\\" . $accData['id'];
            if (!file_exists($accPath)) {
                mkdir($accPath);
                chmod($accPath, 777);
            }

            $uploadPath = $accPath . "\\image\\";
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath);
                chmod($uploadPath, 777);
            }

            if (!move_uploaded_file(
                $tempPath,
                "$uploadPath\\$fileNameHash.$ext"
            )) throw new RuntimeException("Gagal memindahkan gambar");

            // chmod("$uploadPath\\$fileNameHash.$ext", 755);

            # save data to database
            $asetModel = new AsetModel(); 
            $data = [
                $accData['id'],
                $fileNameHash,
                $fileName,
                $urlNameHash,
                $ext,
                $uploadPath,
                $fileSize,
                'image',
                $ext,
                $fileSize,
                '',
                $this->mTimeNow,
                $this->mTimeNow
            ];
            $asetId = $asetModel->insertOne($data);

            return $this->responseOK(['image_id'=>$asetId,'image_key'=>$urlNameHash], 'Upload gambar berhasil');
        } catch (RuntimeException $e) {
            return $this->responseErr(422, $e->getMessage());
        }
    }

    public function put_document()
    {
    }

    public function put_asset()
    {
    }

    public function image(){
        /**
         * This will be return an image
         * 1. access of this method will be like index.php/aset/image/{hash_image} 
         * 2. getting right after parameter after methods parameter
         * 3. then selected image will be provided as well
         */
        header("Acess-Control-Allow-Origin: *");
        $this->methodAllowed("GET");

        try{
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode('/', $uri);
        $image_key = $uri[URL_PARAM_1];
        if(!isset($image_key)) throw new Exception("Parameter gambar tidak ada");

        # search for based image key
        $assetModel = new AsetModel();
        $imageData = $assetModel->findOne($image_key);
        if(count($imageData) < 1) throw new Exception("Gambar tidak dikenal");

        $accFolder = $imageData[0]['account_id'];
        $typeFolder = $imageData[0]['output_type'];
        $filename = $imageData[0]['file_name'];
        $fileExt = $imageData[0]['output_ext'];
        $fileSize = $imageData[0]['file_size'];

        # response image
        $filepath = BASE_PATH."\\assets\\$accFolder\\$typeFolder\\$filename.$fileExt";
        $filemime = pathinfo($filepath, PATHINFO_EXTENSION);
        header("Content-type: $filemime");

        # response as image
        if(false) return imagejpeg($filepath);

        # response as base64 string
        $data = file_get_contents($filepath);
        $base64string = 'data: image/'.$filemime.';base64,'.base64_encode($data);
        echo $base64string;
        
        }catch(Exception $e){
            return $this->responseErr(422, $e->getMessage());
        }
    }

    public function test()
    {
        $mErrorCode = 422;
        $mErrorDesc = '';
        $mReqMethod = $_SERVER['REQUEST_METHOD'];
        $mQueryParam = $this->getQueryStringParam();

        if (strtoupper($mReqMethod) === 'GET') {
            try {
                $asetModel = new AsetModel();

                $reqLimit = 10;
                if (isset($mQueryParam['limit']) && $mQueryParam['limit']) {
                    $reqLimit = $mQueryParam['limit'];
                }

                $asetData = $asetModel->getAll($reqLimit);
            } catch (Error $err) {
                $mErrorDesc = $err->getMessage() . ' Terjadi Kesalahan Server';
                $mErrorCode = 500;
            }
        } else {
            $mErrorDesc = 'Method tidak dikenal';
            $mErrorCode = 422;
        }

        if (!$mErrorDesc) {
            return $this->responseOK($asetData);
        } else {
            return $this->responseErr($mErrorCode, $mErrorDesc);
        }
    }
}
