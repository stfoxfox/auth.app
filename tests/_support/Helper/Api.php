<?php
namespace App\Tests\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Api extends \Codeception\Module
{
    public function getDoctrine()
    {
        $service = $this->getModule('Symfony')->grabServiceFromContainer('doctrine.orm.entity_manager');
        return $service;
    }

    public function getContainer(){
        return $this->getModule('Symfony')->grabService('kernel')->getContainer();
    }
}
