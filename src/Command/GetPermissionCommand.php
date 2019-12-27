<?php

namespace App\Command;

use App\Entity\App;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetPermissionCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('permissions:get')
            ->addArgument('name_role', InputArgument::REQUIRED, 'Name role');
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $name_role = $input->getArgument('name_role');

        if (!$name_role) {
            $io->note(sprintf('You passed an argument name'));
        }

        $role = $em->getRepository('App:Role')->findOneBy(["name" => $name_role]);

        if (!$role) {
            $io->note(sprintf('Role not found: %s', $name_role));
        }

        $permissions = $role->getPermissions();

        foreach ($permissions as $permission) {
            $io->success($permission->getName());
        }

    }
}
