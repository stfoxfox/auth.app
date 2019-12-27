<?php

namespace App\Command;

use App\Entity\App;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateAppCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('app:create')
            ->addArgument('name', InputArgument::REQUIRED, 'Name application')
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

        $publicApplication = new App();
        $publicApplication->setName($name);
        $publicApplication->setKeyApplication(md5(microtime().rand()));
        $publicApplication->setCreatedAt(new \DateTime());
        $publicApplication->setUpdatedAt(new \DateTime());

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->persist($publicApplication);
        $em->flush();

        $io->success('Application created');
    }
}
