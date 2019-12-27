<?php

namespace App\Command;

use App\Entity\App;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteRoleCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('role:delete')
            ->addArgument('name', InputArgument::REQUIRED, 'Delete role')
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

        $role = $em->getRepository('App:Role')->findOneBy(["name" => $name]);

        $em->remove($role);
        $em->flush();

        $io->success('Role delete');
    }
}
