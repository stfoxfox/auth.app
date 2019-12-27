<?php

namespace App\MobileController;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends Controller
{
    /**
     * Endpoint мобильного API, принимающий POST запрос от клиента, содержащий json строку.
     * По значению, определённому в action, вызывается определённый метод.
     * Результат приходит в формате json
     * @param $request Request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $json = $request->getContent();
        if($json == null){
            return new JsonResponse(new Response('Body not found', 404, ''));
        }

        $req = $this->get('service.mobile.request')
            ->setJson($json)
            ->setClientIP($request->getClientIp())
            ->setHeaders($request->headers);

        switch ($req->getAction()) {
            case 'registration-device':
                return new JsonResponse($req->registrationDevice()->getResponse());
                break;
            case 'registration-user':
                return new JsonResponse($req->registrationUser()->getResponse());
                break;
            case 'auth-user':
                return new JsonResponse($req->authUser()->getResponse());
                break;
            case 'auth-fb-user':
                return new JsonResponse($req->authFbUser()->getResponse());
                break;
            default:
                return new JsonResponse(new Response('This action not found', 404, ''));
                break;
        }
    }
}
