<?php

namespace App\Service;

use App\Service\Helper\Request;
use App\Service\Helper\Response;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\Container;
use App\Entity\Role;
use App\Entity\Permission;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;



/**
 * класс, обрабатывающий запросы от приватного API
 */
class PrivateRequest extends Request
{
    /**
     * Обновление JWT токена клиента
     * проверяем json на наличие jwt и clientUUID
     * расшифровываем JWT и проверяем что clientUUID в json т clientUUID в токене совпадают
     * Если нет - записываем в ответ ошибку
     * Иначе генерируем новый токен и записываем clienUUID в ответ
     * @return self
     */
    public function updateToken()
    {
        if (isset(json_decode($this->getRequest())->data) &&
            isset(json_decode($this->getRequest())->data->client_uuid) &&
            isset(json_decode($this->getRequest())->data->jwt_token)
        ) {
            $client_uuid = json_decode($this->getRequest())->data->client_uuid;
            $jwt_token = json_decode($this->getRequest())->data->jwt_token;

            $result = $this->container->get('security.token.service')->updateJwt($jwt_token, $client_uuid);
            if ($result) {
                $this->setResponse(new Response('update-token', 0, $result->getData()));
                return $this;
            }

            $this->setResponse(new Response('clientUUID does not match with clientUUID in JWT token', 8789, ''));
            return $this;
        } else {
            $this->setResponse(new Response('jwt or clientUUID not set', 54543, ''));
            return $this;
        }

        return $this;
    }

    /**
     * получение списка ролей и их прав
     * проверяем json на наличие app_id и app_key
     * если нет - записываем в ответ ошибку
     * проверяем на наличие в БД приложения с такими app_id и app_key
     * если нет - записываем в ответ ошибку
     * находим все роли и их права для этого приложения и записываем их в ответ
     * @return self
     */
    public function getRoles()
    {
        if (isset(json_decode($this->getRequest())->data) &&
            isset(json_decode($this->getRequest())->data->app_key) &&
            isset(json_decode($this->getRequest())->data->app_id)
        ) {
            $app_key = json_decode($this->getRequest())->data->app_key;
            $app_id = json_decode($this->getRequest())->data->app_id;
            $application = $this->em->getRepository('App:App')->findOneBy(['id' => $app_id, 'keyApplication' => $app_key]);
            if(!$application){
                $this->setResponse(new Response('Application with this app_key and app_id not found', 404, ''));
                return $this;
            }

            $roles = $this->em->getRepository('App:Role')->getRolesByApplication($application);
            $result = [];
            foreach($roles as $role){
                $permissions = [];
                foreach($role->getPermissions() as $permission){
                    $permissions[] = $permission->getName();
                }
                $result[] = ['role' => $role->getName(), 'permissions' => $permissions];
            }

            $this->setResponse(new Response('get-roles', 0, $result));
            return $this;
        } else {
            $this->setResponse(new Response('app_key or app_id not set', 54543, ''));
            return $this;
        }

        return $this;
    }
}
