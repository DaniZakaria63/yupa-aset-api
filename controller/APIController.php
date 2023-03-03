<?php

use Carbon\Carbon;

class APIController extends BaseController
{
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
                chmod($accPath, 755);
            }

            $uploadPath = $accPath . "\\images\\";
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath);
                chmod($uploadPath, 755);
            }

            if (!move_uploaded_file(
                $tempPath,
                "$uploadPath.$ext"
            )) throw new RuntimeException("Gagal memindahkan gambar");

            return $this->responseOK(null, 'Upload gambar berhasil');
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
