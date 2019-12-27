<?php

namespace App\Command;

use App\Entity\App;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetAppsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('apps:get');
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

        $apps = $em->getRepository('App:App')->findAll();

        foreach ($apps as $app) {
            $io->success($app->getName());
        }


    }
}
