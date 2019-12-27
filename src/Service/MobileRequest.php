<?php

namespace App\Service;

use App\Entity\Token;
use App\Entity\SocialAccount;
use App\Service\Helper\Request;
use App\Service\Helper\Response;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use App\Entity\Client as DeviceClient;

/**
 * класс, обрабатывающий запросы от мобильного API
 */
class MobileRequest extends Request
{

    /**
     * Заголовки запроса
     * @var array
     */
    private $headers;

    /**
     * IP адрес клиента
     * @var string
     */
    private $client_ip;

    /**
     * @param array $header
     * @return self
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $client_ip
     * @return self
     */
    public function setClientIP($client_ip)
    {
        $this->client_ip = $client_ip;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientIP()
    {
        return $this->client_ip;
    }

    /**
     * Регистрация устройства
     * проверяем json на наличие device_type, device_model и token
     * если не все данные - возвращаем ошибку
     * находим приложение
     * находим клиент
     * если нету - создаём новый
     * если есть - получаем JWT token и пользователя по клиенту
     * записываем ответ
     * @return self
     */
    public function registrationDevice()
    {
        if (isset(json_decode($this->getRequest())->data) &&
            isset(json_decode($this->getRequest())->data->device_type) &&
            isset(json_decode($this->getRequest())->data->device_model) &&
            isset(json_decode($this->getRequest())->data->token)
        ) {
            $device_type = json_decode($this->getRequest())->data->device_type;
            $device_model = json_decode($this->getRequest())->data->device_model;
            $token = json_decode($this->getRequest())->data->token;

            $application = $this->em->getRepository('App:App')->findOneBy(["name" => 'Mobile Api Application']);

            $client = $this->em->getRepository('App:Client')->findOneBy([
                'deviceType' => $device_type,
                'deviceModel' => $device_model,
                'ipAddress' => $this->getClientIP(),
                'tokenPushMessages' => $token,
            ]);

            $response = [];
            if (!$client) {
                $client = $this->container->get('security.authentication.service')
                    ->createClient($this->getClientIP(), $application, $device_type, $device_model);
            } else {
                $jwt_token = $this->em->getRepository('App:Token')->findOneBy(['clientUuid' => $client->getUuid()]);
                if ($jwt_token) {
                    $response = [
                        'jwt_token' => $jwt_token->getJWTToken(),
                        'user_info' => [
                            'user_uuid' => $jwt_token->getUser()->getUuid(),
                        ],
                    ];
                }
            }

            $response['client_uuid'] = $client->getUuid();
            $this->setResponse(new Response('registration-device', 0, $response));

        } else {
            $this->setResponse(new Response('не все данные', 3422, ''));
        }

        return $this;
    }

    /**
     * Регистрация пользователя
     * проверяем json на наличие first_name, second_name, email, password
     * проверяем json на наличие social_id и access_token
     * если есть - проверяем переданный social_id по access_tokeт facebook
     * если не совпадают - возвращаем ошибку
     * если совпадают - проверяем на налиниие такого social_id в БД
     * если есть - возвращаем ошибку
     * если нет - создаём пользователя, авторизируем его, создаём запись в таблице social_account
     * если нету в json social_id и access_token - проверяем на наличие пользователя в БД по email и регистрируем его
     * в ответ записываем clientUUID, JWT token и user UUID 
     * @return self
     */
    public function registrationUser()
    {
        if (isset(json_decode($this->getRequest())->data) &&
            isset(json_decode($this->getRequest())->data->first_name) &&
            isset(json_decode($this->getRequest())->data->second_name) &&
            isset(json_decode($this->getRequest())->data->email) &&
            isset(json_decode($this->getRequest())->data->password)
        ) {
            $first_name = json_decode($this->getRequest())->data->first_name;
            $second_name = json_decode($this->getRequest())->data->second_name;
            $email = json_decode($this->getRequest())->data->email;
            $password = json_decode($this->getRequest())->data->password;

            $user = $this->em->getRepository('App:User')->findOneBy(['email' => $email]);
            if($user){
                $this->setResponse(new Response('пользователь с таким email существует', 888, ''));
                return $this;
            }

            if (strlen($password) < (int) $this->container->getParameter('length_password')) {
                $this->setResponse(new Response('слабый пароль', 999, ''));
                return $this;
            }

            $application = $this->em->getRepository('App:App')->findOneBy(["name" => 'Mobile Api Application']);

            if (
                isset(json_decode($this->getRequest())->data->social_id) &&
                isset(json_decode($this->getRequest())->data->access_token)
            ) {
                $social_id = json_decode($this->getRequest())->data->social_id;
                $access_token = json_decode($this->getRequest())->data->access_token;
                
                $fb = new Facebook([
                    'app_id' => $this->container->getParameter('fbid'),
                    'app_secret' => $this->container->getParameter('fbsecret'),
                    'default_graph_version' => 'v3.1',
                ]);
    
                try {
                    $response = $fb->get('/me?fields=id', $access_token);
                } catch (Facebook\Exceptions\FacebookResponseException $e) {
                    $this->setResponse(new Response('Graph returned an error: ' . $e->getMessage(), 444, ''));
                    return $this;
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    $this->setResponse(new Response('Facebook SDK returned an error: ' . $e->getMessage(), 32, ''));
                    return $this;
                }

                $graph = $response->getGraphUser();
                $fb_social_id = $graph->getId();
                
                if ($fb_social_id == $social_id) {
                    $social = $this->em->getRepository('App:SocialAccount')->findOneBy(['socialId' => $social_id]);
                    if (!$social) {
                        
                        $user = $this->container->get('security.user.service')->createUser($password, $email, $first_name, $second_name);
                        $response = $this->container->get('security.authentication.service')
                            ->authenticate($email, $password, $this->getClientIP(), $application, DeviceClient::TYPE_MOBILE, DeviceClient::TYPE_MOBILE);

                        if ($response->getCode() == 200) {
                            $social = new SocialAccount();
                            $social->setSocialId($social_id)
                                ->setAccessToken($access_token)
                                ->setTokenSecret($this->container->getParameter('fbsecret'))
                                ->setTypeSocialNetwork(SocialAccount::SOCIAL_FACEBOOK)
                                ->setUser($user)
                                ->setCreatedAt(new \DateTime())
                                ->setUpdatedAt(new \DateTime());

                            $this->em->persist($social);
                            $this->em->flush();

                            $this->setResponse(new Response('registration-user', 0, $response->getData()));
                            return $this;
                        }

                        $this->setResponse(new Response($response->getData(), $response->getCode(), ''));
                        return $this;
                    } else {
                        $this->setResponse(new Response('user with this social_id isset', 404, ''));
                        return $this;
                    }
                } else {
                    $this->setReponse(new Response('incorrect socialID or accessToken', 555, ''));
                    return $this;
                }
            }            

            $result = $this->container->get('security.registration.service')->registration(
                $password,
                $email,
                $first_name,
                $second_name,
                $this->getClientIP(),
                $application,
                DeviceClient::TYPE_MOBILE,
                DeviceClient::TYPE_MOBILE
            );

            $this->setResponse(new Response('registration-user', 0, $result->getData()));
        } else {
            $this->setResponse(new Response('не все данные', 3422, ''));
        }

        return $this;
    }

    /**
     * Авторизация пользователя
     * проверяем json на наличие email и password
     * если нет - записываем ошибку
     * авторизовываем пользователя
     * записываем ответ 
     * @return self
     */
    public function authUser()
    {
        if (isset(json_decode($this->getRequest())->data) &&
            isset(json_decode($this->getRequest())->data->email) &&
            isset(json_decode($this->getRequest())->data->password)
        ) {
            $email = json_decode($this->getRequest())->data->email;
            $password = json_decode($this->getRequest())->data->password;

            $result = $this->container->get('security.authentication.service')->authenticate(
                $email,
                $password,
                $this->getClientIP(),
                $this->em->getRepository('App:App')->findOneBy(["name" => 'Mobile Api Application']),
                DeviceClient::TYPE_MOBILE,
                DeviceClient::TYPE_MOBILE
            );

            $this->setResponse(new Response($result->getData(), $result->getCode(), $result->getData()));
        } else {
            $this->setResponse(new Response('не все данные', 3422, ''));
        }

        return $this;
    }

    /**
     * Авторизация пользователя через Facebook
     * проверяем json на наличие social_id и access_token
     * если нет - записывает в ответ ошибку
     * проверяем переданный social_id по access_token
     * если не совпадают - записываем в ответ ошибку
     * проверяем наличие social_id в БД
     * если нет - записываем в ответ ошибку
     * авторизовываем пользователя, созадём клиент, создаём JWT token
     * записываем в ответ clientUUI, JWT token и userUUID
     * @return self
     */
    public function authFbUser()
    {
        if (isset(json_decode($this->getRequest())->data) &&
            isset(json_decode($this->getRequest())->data->social_id) &&
            isset(json_decode($this->getRequest())->data->access_token)
        ) {
            $social_id = json_decode($this->getRequest())->data->social_id;
            $access_token = json_decode($this->getRequest())->data->access_token;

            $fb = new Facebook([
                'app_id' => $this->container->getParameter('fbid'),
                'app_secret' => $this->container->getParameter('fbsecret'),
                'default_graph_version' => 'v3.1',
            ]);

            try {
                $response = $fb->get('/me?fields=id', $access_token);

                // $this->setResponse(new Response('auth-fb-user', 0, $response));
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                $this->setResponse(new Response('Graph returned an error: ' . $e->getMessage(), 444, ''));
                return $this;
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                $this->setResponse(new Response('Facebook SDK returned an error: ' . $e->getMessage(), 32, ''));
                return $this;
            }

            $graph = $response->getGraphUser();
            $fb_social_id = $graph->getId();
            if ($fb_social_id == $social_id) {
                $social = $this->em->getRepository('App:SocialAccount')->findOneBy(['socialId' => $social_id]);
                if ($social) {
                    $user = $social->getUser();
                    $application = $this->em->getRepository('App:App')->findOneBy(["name" => 'Mobile Api Application']);
                    $client = $this->container->get('security.authentication.service')
                        ->createClient($this->getClientIP(), $application, DeviceClient::TYPE_MOBILE, DeviceClient::TYPE_MOBILE);

                    $jwt = $this->container->get('security.token.service')->createJwt($user, $client);
                    $this->container->get('security.token.service')->createToken($user, $client, $jwt);

                    $response = [
                        "clientUuid" => $client->getUuid(),
                        "jwt" => $jwt,
                        "userInfo" => [
                            "userUuid" => $user->getUuid(),
                        ],
                    ];

                    $this->setResponse(new Response('auth-fb-user', 0, $response));
                } else {
                    $this->setResponse(new Response('this user not found', 404, ''));
                }
            } else {
                $this->setReponse(new Response('incorrect socialID or accessToken', 555, ''));
            }
        } else {
            $this->setResponse(new Response('не все данные', 3422, ''));
        }

        return $this;
    }
}
