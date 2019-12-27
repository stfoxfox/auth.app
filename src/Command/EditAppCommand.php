<?php

namespace App\Command;

use App\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EditAppCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('app:edit')
            ->addArgument('app_name', InputArgument::REQUIRED, 'Name application')
            ->addArgument('app_name_new', InputArgument::REQUIRED, 'Name application new')
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

        $appName = $input->getArgument('app_name');
        $appNameNew = $input->getArgument('app_name_new');

        if (!$appNameNew) {
            $io->note(sprintf('You passed an argument name'));
        }

        $application = $em->getRepository('App:App')->findOneBy(["name" => $appName]);

        if (!$application) {
            $io->note(sprintf('Application not found: %s', $appName));
        }

        $application->setName($appNameNew);

        $em->flush();

        $io->success('Application edited');
    }
}
