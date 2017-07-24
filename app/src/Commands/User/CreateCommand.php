<?php

namespace Shortener\Commands\User;

use Shortener\Facade\Users\IAdminUserFacade;
use Shortener\Facade\Users\IClientUserFacade;
use Shortener\Domain\User\UserEntity;
use Shortener\Exception\InvalidStateException;
use Shortener\Facade\Users\IUserFacade;
use Shortener\Security\Roles;
use Shortener\Security\PasswordHash;
use Nette\Utils\ArrayHash;
use Nette\Utils\Random;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create any user command
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Commands\User
 */
class CreateCommand extends Command
{
	/** @var IUserFacade */
	private $userFacade;

	/** @var PasswordHash */
	private $passwordHash;

	/**
	 * CreateUserCommand constructor.
	 *
	 * @param IUserFacade  $userFacade
	 * @param PasswordHash $passwordHash
	 */
	public function __construct(IUserFacade $userFacade, PasswordHash $passwordHash)
	{
		parent::__construct();
		$this->userFacade = $userFacade;
		$this->passwordHash = $passwordHash;
	}

	/**
	 * @inheritdoc
	 */
	protected function configure()
	{
		$this->setName('sh:user:create');
		$this->setDescription('Command for create any user');
		$this->addArgument('email', InputArgument::REQUIRED, 'Specify email address!');
		$this->addArgument('username', InputArgument::REQUIRED, 'Specify username!');
		$this->addArgument('password', InputArgument::OPTIONAL, 'Specify password for account.');
	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 *
	 * @return void
	 * @throws InvalidStateException
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		// read date from console
		$email = $input->getArgument('email');
		$username = $input->getArgument('username');
		$password = $input->getArgument('password');

		$roles = [Roles::ADMIN];

		if ($password === NULL) {
			$password = Random::generate(8);
		}

		$user = $this->userFacade->save([
			'username' => $username,
			'email'    => $email,
			'password' => $this->passwordHash->hashPassword($password),
			'status'   => UserEntity::STATUS_ACTIVE,
			'roles'    => $roles
		]);

		if ($user !== NULL) {
			$output->writeln(sprintf('<info>User created with email %s  and password %s</info>', $email, $password));
		} else {
			$output->writeln(sprintf('<error>User created with email %s  and password %s</error>', $email, $password));

		}
	}
}