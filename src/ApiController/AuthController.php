<?php

namespace App\ApiController;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Helper\Response;

class AuthController extends Controller
{
    /**
     * Endpoint API, принимающий POST запрос от клиента, содержащий json строку.
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
        
        $req = $this->get('service.private.request')->setJson($json);
        switch ($req->getAction()) {
            case 'update-token':
                return new JsonResponse($req->updateToken()->getResponse());
                break;
            case 'get-roles':
                return new JsonResponse($req->getRoles()->getResponse());
                break;
            default:
                return new JsonResponse(new Response('This action not found', 404, ''));
                break;
        }
    }
}
