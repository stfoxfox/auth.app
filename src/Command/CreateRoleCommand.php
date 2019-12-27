<?php

namespace App\Command;

use App\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateRoleCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('role:create')
            ->addArgument('name', InputArgument::REQUIRED, 'Name role')
            ->addArgument('app_name', InputArgument::REQUIRED, 'Name application')
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

        $roleName = $input->getArgument('name');
        $appName = $input->getArgument('app_name');

        if (!$roleName) {
            $io->note(sprintf('You passed an argument name'));
        }

        $application = $em->getRepository('App:App')->findOneBy(["name" => $appName]);

        if (!$application) {
            $io->note(sprintf('Application not found: %s', $appName));
        }

        $role = new Role();
        $role->setName($roleName);
        $role->setApp($application);
        $role->setCreatedAt(new \DateTime());
        $role->setUpdatedAt(new \DateTime());

        $em->persist($role);
        $em->flush();

        $io->success('Role created');
    }
}
