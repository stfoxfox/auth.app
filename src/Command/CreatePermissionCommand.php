<?php

namespace App\Command;

use App\Entity\Permission;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreatePermissionCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('permission:create')
            ->addArgument('name', InputArgument::REQUIRED, 'Name permission')
            ->addArgument('role_name', InputArgument::REQUIRED, 'Name role')
        ;
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $io = new SymfonyStyle($input, $output);

        $permissionName = $input->getArgument('name');
        $roleName = $input->getArgument('role_name');

        if (!$permissionName) {
            $io->note(sprintf('You passed an argument name'));
        }

        $role = $em->getRepository('App:Role')->findOneBy(["name" => $roleName]);

        if (!$role) {
            $io->note(sprintf('Application not found: %s', $roleName));
        }

        $permission = new Permission();
        $permission->setName($permissionName);
        $permission->setCreatedAt(new \DateTime());
        $permission->setUpdatedAt(new \DateTime());

        $em->persist($permission);
        $em->flush();

        $role->addPermission($permission);
        $em->persist($role);
        $em->flush();

        $io->success('Permission created');
    }
}
