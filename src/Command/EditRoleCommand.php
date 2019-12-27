<?php

namespace App\Command;

use App\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EditRoleCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('role:edit')
            ->addArgument('role_name', InputArgument::REQUIRED, 'Name role')
            ->addArgument('role_name_new', InputArgument::REQUIRED, 'Name role new')
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

        $roleName = $input->getArgument('role_name');
        $roleNameNew = $input->getArgument('role_name_new');

        if (!$roleNameNew) {
            $io->note(sprintf('You passed an argument name'));
        }

        $role = $em->getRepository('App:Role')->findOneBy(["name" => $roleName]);

        if (!$role) {
            $io->note(sprintf('Role not found: %s', $roleName));
        }

        $role->setName($roleNameNew);

        $em->flush();

        $io->success('Role edited');
    }
}
