<?php

namespace App\Command;

use App\Entity\App;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteAppCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('app:delete')
            ->addArgument('name', InputArgument::REQUIRED, 'Delete application')
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
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('name');

        if (!$name) {
            $io->note(sprintf('You passed an argument name'));
        }

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $app = $em->getRepository('App:App')->findOneBy(["name" => $name]);

        $roles = $em->getRepository('App:Role')->findBy(["app" => $app]);

        foreach ($roles as $role) {
            $em->remove($role);
        }

        $em->remove($app);
        $em->flush();

        $io->success('Application delete');
    }
}
