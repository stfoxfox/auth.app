<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TokenController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateToken(Request $request)
    {
        $result = $this->get('security.token.service')->updateJwt($request->get('jwt'), $request->get('clientUuid'));
        return new JsonResponse($result->getData(), $result->getCode());
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exampleAction()
    {
        return $this->render('api/example.html.twig');
    }

}
