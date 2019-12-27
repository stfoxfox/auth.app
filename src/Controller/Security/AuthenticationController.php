<?php

namespace App\Controller\Security;

use App\Entity\Client;
use App\Form\LoginForm;
use App\Form\RecoverPasswordForm;
use App\Form\RegistrationForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AuthenticationController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function webAuthenticateAction(Request $request, TranslatorInterface $translator)
    {
        if ($request->cookies->get($this->container->getParameter('cookie_language'))) {
            $translator->setLocale($request->cookies->get($this->container->getParameter('cookie_language')));
        }

        $loginForm = $this->createForm(LoginForm::class);
        $loginForm->handleRequest($request);

        $result = $this->get('security.authentication.service')->authenticate(
            $loginForm['email']->getData(),
            $loginForm['password']->getData(),
            $request->getClientIp(),
            $this->getDoctrine()->getRepository('App:App')->findOneBy(["name" => 'Public application']),
            Client::TYPE_BROWSER,
            Client::TYPE_BROWSER
        );

        if ($result->getCode() != 200) {
            $recoverPasswordForm = $this->createForm(RecoverPasswordForm::class);
            $languages = $this->container->get('service.language')->getLanguages();
            $fbLoginUrl = $this->container->get('security.authentication.service')->getFbAuthUrl($request);

            return $this->render('default/index.html.twig', [
                "registrationForm" => $this->createForm(RegistrationForm::class)->createView(),
                "loginForm" => $loginForm->createView(),
                "recoverPasswordForm" => $recoverPasswordForm->createView(),
                "fbLoginUrl" => $fbLoginUrl,
                "languages" => $languages,
                "error" => $result,
            ]);
        }
        
        $this->get('security.authentication.service')->setCookie($result->getData());
        $ref_url = $request->cookies->get('ref_url');
        if($ref_url !== null){
            setcookie(
                'ref_url',
                null,
                -1,
                '/',
                $this->container->getParameter('cookie_domain')
            );
            return new RedirectResponse($ref_url);
        }

        return $this->redirectToRoute('index_page');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function logout(Request $request)
    {
        $jwt = $this->get('security.token.service')->getJwtFromCookie($request->cookies);

        if ($jwt) {
            $this->get('security.authentication.service')->removeCookie();
            $this->get('security.token.service')->removeToken($jwt);
        }

        return $this->redirectToRoute('index_page');
    }
}
