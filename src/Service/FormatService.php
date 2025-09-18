<?php


namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class FormatService extends AbstractController
{

    public function sendSuccessReponse(mixed $data = null, int $code = 200, bool $compressed = false, array $cookies = []): JsonResponse
    {
        $data = [
            'status' => 'success',
            'data'  => $compressed ? base64_encode(zlib_encode(json_encode($data), ZLIB_ENCODING_DEFLATE)) : $data
        ];

        $response =  $this->json($data, $code);

        if (count($cookies) > 0) {
            foreach ($cookies as $cookie) {
                $response->headers->setCookie($cookie);
            }
        }

        return $response;
    }

    public function sendSuccessSerializeResponse(string $data, int $code = 200, $compressed = false, array $cookies = []) : JsonResponse
    {
        $decodeData = json_decode($data, true);

        $data =  [
            'status' => 'succes',
            'data' => $compressed ? base64_encode(zlib_encode(json_encode($decodeData), ZLIB_ENCODING_DEFLATE)) : $decodeData
        ];

        $response =  $this->json($data, $code);

        if (count($cookies) > 0) {
            foreach ($cookies as $cookie) {
                $response->headers->setCookie($cookie);
            }
        }

        return $response;
    }

    public function sendErrorReponse(string $error, int $errorCode = 500, array $details = [], array $cookies = []): JsonResponse
    {

        $data = [
            'status' => 'error',
            'message'  => $error
        ];

        if (!empty($details)) {
            $data['details'] = $details;
        }

        $response =  $this->json($data, $errorCode);

        if (count($cookies) > 0) {
            foreach ($cookies as $cookie) {
                $response->headers->setCookie($cookie);
            }
        }

        return $response;
    }
}
