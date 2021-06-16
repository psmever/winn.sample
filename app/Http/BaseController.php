<?php
namespace App\Http;

// https://github.com/Intervention/image
use Intervention\Image\ImageManagerStatic as Image;

class BaseController
{
    public static $MediaCategory;
    public static $MediaFile;

    public function __construct()
	{

    }

    public static function baseTest() : string
    {
        return "baseTest";
    }

    public static function checkHeader() : array
    {
        $headers = apache_request_headers();

        $mediaFile = isset($_FILES['media_file']) && $_FILES['media_file'] ? $_FILES['media_file'] : NULL;

        if($mediaFile == NULL) {
            return [
                'state' => false,
                'message' => '미디어 파일이 존재 하지 않습니다.'
            ];
        }

        BaseController::$MediaFile = $mediaFile;

        return [
            'state' => true
        ];
    }

    public static function serverResponse($data = [], $httpCode = 200) {
        // header('Access-Control-Allow-Origin: *');
        // header('Content-type: application/json');
        http_response_code($httpCode);
        echo json_encode( $data );

        exit();
    }

    public static function uuidSecure() {

        $pr_bits = null;
        $fp = @fopen('/dev/urandom','rb');
        if ($fp !== false) {
            $pr_bits .= @fread($fp, 16);
            @fclose($fp);
        } else {
            // If /dev/urandom isn't available (eg: in non-unix systems), use mt_rand().
            $pr_bits = "";
            for($cnt=0; $cnt < 16; $cnt++){
                $pr_bits .= chr(mt_rand(0, 255));
            }
        }

        $time_low = bin2hex(substr($pr_bits,0, 4));
        $time_mid = bin2hex(substr($pr_bits,4, 2));
        $time_hi_and_version = bin2hex(substr($pr_bits,6, 2));
        $clock_seq_hi_and_reserved = bin2hex(substr($pr_bits,8, 2));
        $node = bin2hex(substr($pr_bits,10, 6));

        /**
         * Set the four most significant bits (bits 12 through 15) of the
         * time_hi_and_version field to the 4-bit version number from
         * Section 4.1.3.
         * @see http://tools.ietf.org/html/rfc4122#section-4.1.3
         */
        $time_hi_and_version = hexdec($time_hi_and_version);
        $time_hi_and_version = $time_hi_and_version >> 4;
        $time_hi_and_version = $time_hi_and_version | 0x4000;

        /**
         * Set the two most significant bits (bits 6 and 7) of the
         * clock_seq_hi_and_reserved to zero and one, respectively.
         */
        $clock_seq_hi_and_reserved = hexdec($clock_seq_hi_and_reserved);
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved >> 2;
        $clock_seq_hi_and_reserved = $clock_seq_hi_and_reserved | 0x8000;

        return sprintf('%08s-%04s-%04x-%04x-%012s',
            $time_low, $time_mid, $time_hi_and_version, $clock_seq_hi_and_reserved, $node);
    }

    public static function imageResize(String $sourceFilePath, String $tagetFilePath, Int $Width, Int $Height)
    {
        try {

            Image::configure(array('driver' => 'imagick'));

            list($fileWidth, $fileHeight) = getimagesize($sourceFilePath);

            if ($Width > $fileWidth && $Height > $fileHeight) {
                move_uploaded_file($sourceFilePath, $tagetFilePath);
                return [
                    'state' => true
                ];
            }

            $r = $fileWidth / $fileHeight;

            if ($Width / $Height > $r) {
                $newwidth = $Height * $r;
                $newheight = $Height;
            } else {
                $newheight = $Width / $r;
                $newwidth = $Width;
            }

            $img = Image::make($sourceFilePath);
            $img->resize($newwidth, $newheight);
            $img->save($tagetFilePath);

        } catch (\Exception $exception){
            return [
                'state' => false,
                'error' => $exception->getMessage()
            ];
        }

        return [
            'state' => true
        ];
    }
}
