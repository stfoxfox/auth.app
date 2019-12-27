<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RoleController extends Controller
{

    /**
     * @param Request $request
     * @return \App\Classes\Security\SecurityResponseObject
     */
    public function getRoles(Request $request)
    {
        $appId = $request->get('app_id');
        $apiKey = $request->get('api_key');

        return $this->get('security.role.service')->getRoles($appId, $apiKey);
    }

}
