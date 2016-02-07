<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 2/7/16
 * Time: 4:16 PM
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\User;

class CreateUserWithAdminRoleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('create:blog:admin')
            ->setDescription('Create user with admin role')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'username'
            )
            ->addArgument(
                'password',
                InputArgument::REQUIRED,
                'password'
            )
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'email'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();

        $user->setUsername($input->getArgument('username'));
        $user->setEmail($input->getArgument('email'));
        $user->setRoles(['ROLE_ADMIN']);

        $password = $this->getContainer()->get('security.password_encoder')
            ->encodePassword($user, $input->getArgument('password'));
        $user->setPassword($password);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
    }
}