<?php


namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class FormatService extends AbstractController
{

    public function sendSuccessJsonReponse(mixed $data, $code = 200, bool $compressed = false): JsonResponse
    {

        $data = [
            'status' => 'success',
            'data'  => $compressed ? base64_encode(zlib_encode(json_encode($data), ZLIB_ENCODING_DEFLATE)) : $data
        ];

        return $this->json($data,$code);
    }

    public function sendErrorJsonReponse($error, $errorCode = 500, $details = []): JsonResponse
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
