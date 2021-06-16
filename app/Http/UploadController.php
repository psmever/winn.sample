<?php

namespace App\Http;

use App\Http\BaseController;
use Spatie\PdfToText\Pdf;
use Smalot\PdfParser\Parser;

class UploadController extends BaseController
{
    public static $uploadCategory;

    public function __construct($fileCategory = '')
    {
        self::$uploadCategory = $fileCategory;


        $check = self::checkHeader();


    }

    public function start()
    {
        $uploadCategory =  self::$uploadCategory;

        $newfileBasename = BaseController::uuidSecure();

        $MediaFile = BaseController::$MediaFile;


        if (isset($MediaFile['error']) && $MediaFile['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $MediaFile['tmp_name'];
            $fileName = $MediaFile['name'];
            $fileSize = $MediaFile['size'];
            $fileType = $MediaFile['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $newFileName = $newfileBasename . '.' . $fileExtension;
            $newSubDir = sha1(date("Ymd"));

            $allowedfileExtensions = array('pdf', 'xls');

            if (in_array($fileExtension, $allowedfileExtensions)) {
                $baseDirectory = "/storage/{$uploadCategory}/" . $newSubDir;
                $uploadFileDir = $_SERVER["DOCUMENT_ROOT"] . $baseDirectory;
                $uploadFileDestpath = $baseDirectory;
                $uploadFileURL = $baseDirectory;

                try {

                    if(!is_dir($uploadFileDir)){
                        if(!mkdir($uploadFileDir, 0777, true)) {
                            BaseController::serverResponse([
                                'state' => false,
                                'message' => '처리중 문제가 발생 했습니다. (005)',
                            ], 500);
                        }
                    }

                    $dest_path = $uploadFileDir . "/" . $newFileName;
                    $dest_url = $uploadFileURL . "/" . $newFileName;

                    $resizeResult = BaseController::imageResize($fileTmpPath, $dest_path, 1024, 768);

                    if($resizeResult['state'] == true) {
                        $uploadFileURL = PROTOCOL . $_SERVER["HTTP_HOST"] . $dest_url;


                        $uploadFileFullPath = $_SERVER['DOCUMENT_ROOT'] .'/'. $uploadFileDestpath.'/'.$newFileName;


                        // echo Pdf::getText($uploadFileFullPath);


                        $text = (new Pdf('/usr/local/bin/pdftotext'))
                        ->setPdf($uploadFileFullPath)
                        ->text();


                        echo "<pre>";
                        print_r($text);


                        // $parser = new \Smalot\PdfParser\Parser();
                        // $pdf    = $parser->parseFile($uploadFileFullPath);

                        // // Retrieve all pages from the pdf file.
                        // $pages  = $pdf->getPages();

                        // // Loop over each page to extract text.
                        // foreach ($pages as $page) {
                        //     echo $page->getText();
                        // }





                        // BaseController::serverResponse([
                        //     'state' => true,
                        //     'data' => [
                        //         'media_url' => $uploadFileURL,
                        //         'dest_path' => $uploadFileDestpath,
                        //         'new_file_name' => $newFileName,
                        //         'original_name' => $fileName,
                        //         'file_type' => $fileType,
                        //         'file_size' => $fileSize,
                        //         'file_extension' => $fileExtension,
                        //     ]
                        // ], 201);


                        return;
                    } else {
                        BaseController::serverResponse([
                            'state' => false,
                            'message' => '처리중 문제가 발생 했습니다. (004)',
                            'error' => $resizeResult['error']
                        ], 500);
                        return;
                    }

                } catch (\Exception $exception){
                    BaseController::serverResponse([
                        'state' => false,
                        'message' => '처리중 문제가 발생 했습니다. (003)',
                        'error' => $exception->getMessage()
                    ], 500);
                    return;
                }
            } else {
                BaseController::serverResponse([
                    'state' => false,
                    'message' => '처리중 문제가 발생 했습니다. (002)',
                    'error' => 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions)
                ], 400);
            }
        } else {
            BaseController::serverResponse([
                'state' => false,
                'message' => '처리중 문제가 발생 했습니다. (001)',
                'error' => $_FILES['image']['error']
            ], 400);
        }
    }
}
