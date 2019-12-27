<?php

namespace App\Command;

use App\Entity\App;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetRolesCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('roles:get')
            ->addArgument('name_app', InputArgument::REQUIRED, 'Name application');
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

        $name_app = $input->getArgument('name_app');

        if (!$name_app) {
            $io->note(sprintf('You passed an argument name'));
        }

        $app = $em->getRepository('App:App')->findOneBy(["name" => $name_app]);

        if (!$app) {
            $io->note(sprintf('Application not found: %s', $name_app));
        }

        $roles = $em->getRepository('App:Role')->findBy(["app" => $app]);

        foreach ($roles as $role) {
            $io->success($role->getName());
        }

    }
}
