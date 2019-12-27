<?php

namespace App\Command;

use App\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EditPermissionCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('permission:edit')
            ->addArgument('permission_name', InputArgument::REQUIRED, 'Name permission')
            ->addArgument('permission_name_new', InputArgument::REQUIRED, 'Name permission new')
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

        $permissionName = $input->getArgument('permission_name');
        $permissionNameNew = $input->getArgument('permission_name_new');

        if (!$permissionNameNew) {
            $io->note(sprintf('You passed an argument name'));
        }

        $permission = $em->getRepository('App:Permission')->findOneBy(["name" => $permissionName]);

        if (!$permission) {
            $io->note(sprintf('Permission not found: %s', $permissionName));
        }

        $permission->setName($permissionNameNew);

        $em->flush();

        $io->success('Permission edited');
    }
}
