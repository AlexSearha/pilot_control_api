<?php


namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class FormatService extends AbstractController
{

    public function sendSuccessReponse(mixed $data, $code = 200, bool $compressed = false): JsonResponse
    {
        $data = [
            'status' => 'success',
            'data'  => $compressed ? base64_encode(zlib_encode(json_encode($data), ZLIB_ENCODING_DEFLATE)) : $data
        ];

        return $this->json($data,$code);
    }

    public function sendSuccessSerializeResponse(string $data, $code = 200, $compressed = false) : JsonResponse
    {
        $decodeData = json_decode($data, true);

        $data =  [
            'status' => 'succes',
            'data' => $compressed ? base64_encode(zlib_encode(json_encode($decodeData), ZLIB_ENCODING_DEFLATE)) : $decodeData
        ];

        return $this->json($data, $code);
    }

    public function sendErrorReponse($error, $errorCode = 500, $details = []): JsonResponse
    {

        $data = [
            'status' => 'error',
            'message'  => $error
        ];

        if (!empty($details)) {
            $data['details'] = $details;
        }

        return $this->json($data,$errorCode);
    }
}
