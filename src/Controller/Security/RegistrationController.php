<?php

namespace App\Controller\Security;

use App\Entity\Client;
use App\Form\LoginForm;
use App\Form\RecoverPasswordForm;
use App\Form\RegistrationForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class RegistrationController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function registrationAction(Request $request, TranslatorInterface $translator)
    {
        if ($request->cookies->get($this->container->getParameter('cookie_language'))) {
            $translator->setLocale($request->cookies->get($this->container->getParameter('cookie_language')));
        }

        $registerForm = $this->createForm(RegistrationForm::class);
        $registerForm->handleRequest($request);

        $result = $this->get('security.registration.service')->registration(
            $registerForm['password']->getData(),
            $registerForm['email']->getData(),
            $registerForm['name']->getData(),
            $registerForm['surname']->getData(),
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
                "registrationForm" => $registerForm->createView(),
                "loginForm" => $this->createForm(LoginForm::class)->createView(),
                "error" => $result,
                "fbLoginUrl" => $fbLoginUrl,
                "languages" => $languages,
                "recoverPasswordForm" => $recoverPasswordForm->createView(),
            ]);
        }

        $this->get('security.authentication.service')->setCookie($result->getData());

        return $this->redirectToRoute('index_page');
    }

    /**
     * @param $token
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function registrationConfirm($token)
    {
        $result = $this->get('security.registration.service')->registrationConfirm($token);
        return $this->render('Security/registration_confirm.html.twig', $result);
    }
}
