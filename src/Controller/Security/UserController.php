<?php

namespace App\Controller\Security;

use App\Form\ChangeEmailForm;
use App\Form\ChangePasswordForm;
use App\Form\LoginForm;
use App\Form\RecoverPasswordForm;
use App\Form\RegistrationForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return \App\Classes\Security\SecurityResponseObject
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function sendMessageForRecoverPassword(Request $request, TranslatorInterface $translator)
    {
        if ($request->cookies->get($this->container->getParameter('cookie_language'))) {
            $translator->setLocale($request->cookies->get($this->container->getParameter('cookie_language')));
        }

        $recoverPasswordForm = $this->createForm(RecoverPasswordForm::class);
        $recoverPasswordForm->handleRequest($request);

        $result = $this->get('security.user.service')->sendMessageRecoverPassword($recoverPasswordForm['email']->getData());

        $registerForm = $this->createForm(RegistrationForm::class);
        $loginForm = $this->createForm(LoginForm::class);
        $recoverPasswordForm = $this->createForm(RecoverPasswordForm::class);
        $fbLoginUrl = $this->container->get('security.authentication.service')->getFbAuthUrl($request);

        $languages = $this->container->get('service.language')->getLanguages();

        return $this->render('default/index.html.twig', [
            "registrationForm" => $registerForm->createView(),
            "loginForm" => $loginForm->createView(),
            "recoverPasswordForm" => $recoverPasswordForm->createView(),
            "fbLoginUrl" => $fbLoginUrl,
            "languages" => $languages,
            "send_message_response" => $result
        ]);
    }

    /**
     * @param Request $request
     * @param $token
     * @return \App\Classes\Security\SecurityResponseObject
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function recoverPassword(Request $request, TranslatorInterface $translator, $token)
    {
        if ($request->cookies->get($this->container->getParameter('cookie_language'))) {
            $translator->setLocale($request->cookies->get($this->container->getParameter('cookie_language')));
        }

        $result = $this->get('security.user.service')->recoverPassword($token, $request->get('password'));
        if($result->getCode() !== 200){
            return $this->redirectToRoute('index_page');
        }
        $changePasswordForm = $this->createForm(ChangePasswordForm::class);
        $changeEmailForm = $this->createForm(ChangeEmailForm::class);
        
        return $this->render('Security/recover_password.html.twig', [
            "changePasswordForm" => $changePasswordForm->createView(),
            "changeEmailForm" => $changeEmailForm->createView(),
            "is_modal" => 1,
            "token" => $token
        ]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     * @throws \Exception
     */
    public function sendMessageForChangeEmail(Request $request, TranslatorInterface $translator)
    {
        if ($request->cookies->get($this->container->getParameter('cookie_language'))) {
            $translator->setLocale($request->cookies->get($this->container->getParameter('cookie_language')));
        }

        if ($request->cookies->get('AUTHKONSTRUKTOR')) {
            $data = json_decode($request->cookies->get('AUTHKONSTRUKTOR'));
            $user = $this->get('security.user.service')->getUserByJwt($data->jwt);
            if ($user) {
                $changePasswordForm = $this->createForm(ChangePasswordForm::class);
                $changeEmailForm = $this->createForm(ChangeEmailForm::class);
                $changeEmailForm->handleRequest($request);

                $result = $this->get('security.user.service')->sendMessageChangeEmail($user, $changeEmailForm['email']->getData());

                if ($result->getCode() != 200) {
                    return $this->render('default/userProfile.html.twig', [
                        "changePasswordForm" => $changePasswordForm->createView(),
                        "changeEmailForm" => $changeEmailForm->createView(),
                        "error" => $result,
                    ]);
                }

                return $this->render('default/messageChangeEmail.html.twig');
            }
        }

        return $this->redirectToRoute('index_page');
    }

    /**
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeEmail($token)
    {
        $result = $this->get('security.user.service')->changeEmail($token);

        return $this->render('default/completeChangeEmail.html.twig', [
            "result" => $result,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     */
    public function changePassword(Request $request, TranslatorInterface $translator, $token = null)
    {
        if ($request->cookies->get($this->container->getParameter('cookie_language'))) {
            $translator->setLocale($request->cookies->get($this->container->getParameter('cookie_language')));
        }

        if ($request->cookies->get('AUTHKONSTRUKTOR')) {
            $data = json_decode($request->cookies->get('AUTHKONSTRUKTOR'));
            $user = $this->get('security.user.service')->getUserByJwt($data->jwt);
            if ($user) {
                $changePasswordForm = $this->createForm(ChangePasswordForm::class);
                $changePasswordForm->handleRequest($request);

                $changeEmailForm = $this->createForm(ChangeEmailForm::class);

                $result = $this->get('security.user.service')->changePassword(
                    $user, $changePasswordForm['password']->getData(), $changePasswordForm['confirm_password']->getData());

                if ($result->getCode() != 200) {
                    return $this->render('default/userProfile.html.twig', [
                        "changePasswordForm" => $changePasswordForm->createView(),
                        "changeEmailForm" => $changeEmailForm->createView(),
                        "error" => $result,
                    ]);
                }
            }
        }

        if($token !== null){
            $user = $this->getDoctrine()->getManager()->getRepository('App:User')->findOneBy(["tokenResetPassword" => $token]);
            if($user !== null){
                $changePasswordForm = $this->createForm(ChangePasswordForm::class);
                $changePasswordForm->handleRequest($request);
                $result = $this->get('security.user.service')->changePassword(
                    $user, $changePasswordForm['password']->getData(), $changePasswordForm['confirm_password']->getData());
                $user->setTokenResetPassword('');
                $this->getDoctrine()->getManager()->flush($user);
            }
        }

        return $this->redirectToRoute('index_page');
    }

}
