<?php

namespace App\Controller;

use App\Classes\Security\SecurityNotifications;
use App\Entity\SocialAccount;
use App\Form\ChangeEmailForm;
use App\Form\ChangePasswordForm;
use App\Form\LoginForm;
use App\Form\RecoverPasswordForm;
use App\Form\RegistrationForm;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException
     */
    public function indexAction(Request $request, TranslatorInterface $translator, $is_modal = null)
    {
        if ($request->cookies->get($this->container->getParameter('cookie_language'))) {
            $translator->setLocale($request->cookies->get($this->container->getParameter('cookie_language')));
        }

        $fbLoginUrl = $this->container->get('security.authentication.service')->getFbAuthUrl($request);

        if ($request->cookies->get('AUTHKONSTRUKTOR')) {
            $data = json_decode($request->cookies->get('AUTHKONSTRUKTOR'));
            $user = $this->container->get('security.user.service')->getUserByJwt($data->jwt);
            if ($user) {
                $changePasswordForm = $this->createForm(ChangePasswordForm::class);
                $changeEmailForm = $this->createForm(ChangeEmailForm::class);
                
                return $this->render('default/userProfile.html.twig', [
                    "changePasswordForm" => $changePasswordForm->createView(),
                    "changeEmailForm" => $changeEmailForm->createView(),
                    "is_modal" => $is_modal,
                    "token" => 'null'
                ]);
            }
        }

        $registerForm = $this->createForm(RegistrationForm::class);
        $loginForm = $this->createForm(LoginForm::class);
        $recoverPasswordForm = $this->createForm(RecoverPasswordForm::class);

        $languages = $this->container->get('service.language')->getLanguages();

        return $this->render('default/index.html.twig', [
            "registrationForm" => $registerForm->createView(),
            "loginForm" => $loginForm->createView(),
            "recoverPasswordForm" => $recoverPasswordForm->createView(),
            "fbLoginUrl" => $fbLoginUrl,
            "languages" => $languages
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeLanguageAction(Request $request)
    {
        setcookie('language', $request->get('language'));
        return $this->redirectToRoute('index_page');
    }

    /**
     * @return JsonResponse
     */
    public function test()
    {
        $item = $this->getDoctrine()->getManager()->getRepository('App:User')->findOneBy(['email' => 'example@example.com']);

        return new JsonResponse($item);
    }

    public function fbCallback(Request $request)
    {
        $fb = new Facebook([
            'app_id' => $this->container->getParameter('fbid'),
            'app_secret' => $this->container->getParameter('fbsecret'),
            'default_graph_version' => 'v2.10',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
            $response = $fb->get('/me?fields=id,name,email,first_name,last_name', $accessToken);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            return new Response('Graph returned an error: ' . $e->getMessage());
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            return new Response('Facebook SDK returned an error: ' . $e->getMessage());
        }

        if (!isset($accessToken)) {
            return new Response($helper->getError());
        }

        $graph = $response->getGraphUser();

        $social_id = $graph->getId();
        $email = $graph->getEmail();
        $first_name = $graph->getFirstName();
        $second_name = $graph->getLastName();

        $application = $this->getDoctrine()->getRepository('App:App')->findOneBy(["name" => 'Public Api Application']);

        if ($email !== null && $first_name !== null && $second_name !== null) {
            $social = $this->getDoctrine()->getRepository('App:SocialAccount')->findOneBy(['socialId' => $social_id]);
            if (!$social) {

                if ($this->getDoctrine()->getRepository('App:User')->findOneBy(["email" => $email])) {
                    return SecurityNotifications::getErrorResponse(SecurityNotifications::EXIST_EMAIL);
                }

                $password = $this->container->get('security.user.service')->generateRandomString(8);

                $user = $this->container->get('security.user.service')->createUser($password, $email, $first_name, $second_name);

                $response = $this->container->get('security.authentication.service')
                    ->authenticate($email, $password, $request->getClientIp(), $application, 1, 1);

                if ($response->getCode() == 200) {
                    $social = new SocialAccount();
                    $social->setSocialId($social_id)
                        ->setAccessToken($accessToken)
                        ->setTokenSecret($this->container->getParameter('fbsecret'))
                        ->setTypeSocialNetwork(SocialAccount::SOCIAL_FACEBOOK)
                        ->setUser($user)
                        ->setCreatedAt(new \DateTime())
                        ->setUpdatedAt(new \DateTime());

                    $this->getDoctrine()->getManager()->persist($social);
                    $this->getDoctrine()->getManager()->flush();

                    $this->container->get('security.authentication.service')->setCookie($response->getData());

                    return $this->redirectToRoute('index_page');
                }

            } else {
                $user = $social->getUser();

                $client = $this->container->get('security.authentication.service')
                    ->createClient($request->getClientIp(), $application, 1, 1);

                $jwt = $this->container->get('security.token.service')->createJwt($user, $client);
                $this->container->get('security.token.service')->createToken($user, $client, $jwt);
                $cookie = [
                    "clientUuid" => $client->getUuid(),
                    "jwt" => $jwt,
                    "userInfo" => [
                        "userUuid" => $user->getUuid(),
                    ],
                ];
                $this->container->get('security.authentication.service')
                    ->setCookie(SecurityNotifications::getSuccessResponse($cookie)->getData());

                return $this->redirectToRoute('index_page');
            }

        }

        return $this->redirectToRoute('index_page');
    }

}
