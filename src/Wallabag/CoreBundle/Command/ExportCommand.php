<?php

namespace Wallabag\CoreBundle\Command;

use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('wallabag:export')
            ->setDescription('Export all entries for an user')
            ->setHelp('This command helps you to export all entries for an user')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'User from which to export entries'
            )
            ->addArgument(
                'filepath',
                InputArgument::OPTIONAL,
                'Path of the exported file'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $user = $this->get('wallabag_user.user_repository')->findOneByUserName($input->getArgument('username'));
        } catch (NoResultException $e) {
            $output->writeln(sprintf('<error>User "%s" not found.</error>', $input->getArgument('username')));

            return 1;
        }

        $entries = $this->get('wallabag_core.entry_repository')
            ->getBuilderForAllByUser($user->getId())
            ->getQuery()
            ->getResult();

        $output->write(sprintf('Exporting %d entrie(s) for user « <comment>%s</comment> »... ', count($entries), $user->getUserName()));

        $filePath = $input->getArgument('filepath');

        if (!$filePath) {
            $filePath = $this->getContainer()->getParameter('kernel.root_dir').'/../'.sprintf('%s-export.json', $user->getUsername());
        }

        try {
            $data = $this->getContainer()->get('wallabag_core.helper.entries_export')
                ->setEntries($entries)
                ->updateTitle('All')
                ->exportJsonData();
            file_put_contents($filePath, $data);
        } catch (\InvalidArgumentException $e) {
            $output->writeln(sprintf('<error>Error: "%s"</error>', $e->getMessage()));

            return 1;
        }

        $output->writeln('<info>Done.</info>');

        return 0;
    }

    private function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }
}
