<?php

namespace Shortener\Facade\Users;

use DoctrineMapper\ArrayAccessEntityMapper;
use DoctrineMapper\Parsers\Date\DateParser;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\EntityRepository;
use Nette\Utils\Strings;
use Shortener\Application\Doctrine\Facade\BaseStatusFacade;
use Shortener\Application\Filter\BaseStatusFilter;
use Shortener\Application\Filter\IFilter;
use Shortener\Application\ResultSet\DoctrineResultSet;
use Shortener\Configuration\Configuration;
use Shortener\Domain\User\UserEntity;
use Shortener\Domain\User\UserRepository;
use Shortener\Exception\EmailExistsException;
use Shortener\Exception\UsernameExistsException;
use Shortener\Security\PasswordHash;
use Nette\Application\LinkGenerator;
use Nette\Utils\Random;

/**
 * User facade
 *
 * @author  Hynek Nerad <iam@kennydaren.me>
 * @package Shortener\Facade\Users
 */
class UserFacade extends BaseStatusFacade implements IUserFacade
{
	/** @var UserRepository */
	protected $userRepository;

	/** @var PasswordHash */
	private $passwordHash;

	/** @var Configuration */
	private $configuration;

	/** @var LinkGenerator */
	private $linkGenerator;

	/**
	 * BaseUserFacade constructor.
	 *
	 * @param EntityManager           $entityManager
	 * @param ArrayAccessEntityMapper $arrayAccessMapper
	 * @param DateParser              $dateParser
	 * @param UserRepository          $baseUserRepository
	 * @param PasswordHash            $passwordHash
	 * @param Configuration           $configuration
	 * @param LinkGenerator           $linkGenerator
	 */
	public function __construct(EntityManager $entityManager,
	                            ArrayAccessEntityMapper $arrayAccessMapper,
	                            DateParser $dateParser,
	                            UserRepository $baseUserRepository,
	                            PasswordHash $passwordHash,
	                            Configuration $configuration,
	                            LinkGenerator $linkGenerator)
	{
		parent::__construct($entityManager, $arrayAccessMapper);
		$this->userRepository = $baseUserRepository;
		$this->passwordHash = $passwordHash;
		$this->configuration = $configuration;
		$this->linkGenerator = $linkGenerator;
	}

	/**
	 * Find user by email
	 *
	 * @param string $email
	 *
	 * @return UserEntity|NULL
	 */
	public function findByEmail(string $email)
	{
		return $this->createQueryBuilder('c')
			->andWhere('c.email = :email')
			->setParameter('email', $email)
			->addOrderBy('c.id', 'DESC')// because soft delete
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult();
	}

	/**
	 * Find user by username
	 *
	 * @param string $username
	 *
	 * @return UserEntity|NULL
	 *
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findByUsername(string $username)
	{
		return $this->createQueryBuilder('c')
			->andWhere('c.username = :username')
			->setParameter('username', $username)
			->addOrderBy('c.id', 'DESC')// because soft delete
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult();
	}

	/**
	 * Filter by filter definition
	 *
	 * @param IFilter $filter
	 *
	 * @return DoctrineResultSet
	 */
	public function find(IFilter $filter)
	{
		/** @var BaseStatusFilter $filter */
		$qb = $this->createQueryBuilder('u', $filter);

		return new DoctrineResultSet($qb);
	}

	/**
	 * Find user by generated token
	 *
	 * @param string $token
	 *
	 * @return UserEntity|NULL
	 */
	public function findUserByToken(string $token)
	{
		return $this->createQueryBuilder('u')
			->andWhere('u.token = :token')
			->setParameter('token', $token)
			->getQuery()
			->getOneOrNullResult();
	}

	/**
	 * @param array $values
	 *
	 * @return UserEntity
	 * @throws EmailExistsException
	 * @throws UsernameExistsException
	 */
	public function create(array $values)
	{
		$values['email'] = Strings::lower($values['email']);
		$user = $this->findByEmail($values['email']);

		if ($user !== NULL) {
			throw new EmailExistsException(sprintf('User with email %s already exists',
				$values['email']));
		}

		$values['username'] = Strings::lower($values['username']);
		$user = $this->findByUsername($values['username']);

		if ($user !== NULL) {
			throw new UsernameExistsException(sprintf('User with email %s already exists',
				$values['username']));
		}

		$values['password'] = $this->passwordHash->hashPassword($values['password']);
		$values['token'] = $token = $this->generateUserToken();
		$values['status'] = UserEntity::STATUS_ACTIVE;

		/** @var UserEntity $user */
		$user = $this->save($values);

		return $user;
	}

	/**
	 * @inheritdoc
	 */
	protected function getClass(): string
	{
		return UserEntity::class;
	}

	/**
	 * @inheritdoc
	 */
	protected function getRepository(): EntityRepository
	{
		return $this->userRepository;
	}

	/**
	 * Generates user token
	 *
	 * @return string
	 */
	private function generateUserToken(): string
	{
		//generate not existing token
		do {
			$token = Random::generate(32);
			$user = $this->findUserByToken($token);
		} while ($user !== NULL);

		return $token;
	}


}