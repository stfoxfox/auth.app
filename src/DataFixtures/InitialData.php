<?php

namespace App\DataFixtures;

use App\Entity\App;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class InitialData extends Fixture
{
    /**
     * Создание ролей, приложений в БД
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $publicApplication = new App();
        $publicApplication->setName('Public application');
        $publicApplication->setKeyApplication(md5(microtime() . rand()));
        $publicApplication->setCreatedAt(new \DateTime());
        $publicApplication->setUpdatedAt(new \DateTime());
        $manager->persist($publicApplication);

        $privateApplication = new App();
        $privateApplication->setName('Private application');
        $privateApplication->setKeyApplication(md5(microtime() . rand()));
        $privateApplication->setCreatedAt(new \DateTime());
        $privateApplication->setUpdatedAt(new \DateTime());
        $manager->persist($privateApplication);

        $roleGuestPublicApp = new Role();
        $roleGuestPublicApp->setName('guest');
        $roleGuestPublicApp->setCreatedAt(new \DateTime());
        $roleGuestPublicApp->setUpdatedAt(new \DateTime());
        $roleGuestPublicApp->setApp($publicApplication);
        $manager->persist($roleGuestPublicApp);

        $roleGuestPrivateApp = new Role();
        $roleGuestPrivateApp->setName('guest');
        $roleGuestPrivateApp->setCreatedAt(new \DateTime());
        $roleGuestPrivateApp->setUpdatedAt(new \DateTime());
        $roleGuestPrivateApp->setApp($privateApplication);
        $manager->persist($roleGuestPrivateApp);

        $roleUserPublicApp = new Role();
        $roleUserPublicApp->setName('user');
        $roleUserPublicApp->setCreatedAt(new \DateTime());
        $roleUserPublicApp->setUpdatedAt(new \DateTime());
        $roleUserPublicApp->setApp($publicApplication);
        $manager->persist($roleUserPublicApp);

        $roleUserPrivateApp = new Role();
        $roleUserPrivateApp->setName('user');
        $roleUserPrivateApp->setCreatedAt(new \DateTime());
        $roleUserPrivateApp->setUpdatedAt(new \DateTime());
        $roleUserPrivateApp->setApp($privateApplication);
        $manager->persist($roleUserPrivateApp);

        $roleOwnerPublicApp = new Role();
        $roleOwnerPublicApp->setName('owner');
        $roleOwnerPublicApp->setCreatedAt(new \DateTime());
        $roleOwnerPublicApp->setUpdatedAt(new \DateTime());
        $roleOwnerPublicApp->setApp($publicApplication);
        $manager->persist($roleOwnerPublicApp);

        $roleOwnerPrivateApp = new Role();
        $roleOwnerPrivateApp->setName('owner');
        $roleOwnerPrivateApp->setCreatedAt(new \DateTime());
        $roleOwnerPrivateApp->setUpdatedAt(new \DateTime());
        $roleOwnerPrivateApp->setApp($privateApplication);
        $manager->persist($roleOwnerPrivateApp);

        $roleGroupAdminPublicApp = new Role();
        $roleGroupAdminPublicApp->setName('group_admin');
        $roleGroupAdminPublicApp->setCreatedAt(new \DateTime());
        $roleGroupAdminPublicApp->setUpdatedAt(new \DateTime());
        $roleGroupAdminPublicApp->setApp($publicApplication);
        $manager->persist($roleGroupAdminPublicApp);

        $roleGroupAdminPrivateApp = new Role();
        $roleGroupAdminPrivateApp->setName('group_admin');
        $roleGroupAdminPrivateApp->setCreatedAt(new \DateTime());
        $roleGroupAdminPrivateApp->setUpdatedAt(new \DateTime());
        $roleGroupAdminPrivateApp->setApp($privateApplication);
        $manager->persist($roleGroupAdminPrivateApp);

        $roleGroupMemberPublicApp = new Role();
        $roleGroupMemberPublicApp->setName('group_member');
        $roleGroupMemberPublicApp->setCreatedAt(new \DateTime());
        $roleGroupMemberPublicApp->setUpdatedAt(new \DateTime());
        $roleGroupMemberPublicApp->setApp($publicApplication);
        $manager->persist($roleGroupMemberPublicApp);

        $roleGroupMemberPrivateApp = new Role();
        $roleGroupMemberPrivateApp->setName('group_member');
        $roleGroupMemberPrivateApp->setCreatedAt(new \DateTime());
        $roleGroupMemberPrivateApp->setUpdatedAt(new \DateTime());
        $roleGroupMemberPrivateApp->setApp($privateApplication);
        $manager->persist($roleGroupMemberPrivateApp);

        $manager->flush();
    }
}
