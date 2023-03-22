<?php
namespace App\Models;
use Nette;
use Nette\Security\IIdentity as IIdentity;


class Auth implements Nette\Security\IAuthenticator
{
	public $database;
	public $passwords;

	public function __construct(Nette\Database\Connection $database)
	{
		$this->database = $database;
		//$this->passwords = $passwords;
	}

	public function authenticate(array $credentials): IIdentity
	{
		[$username, $password] = $credentials;

        $row = $this->database->table('users')
			->where('username', $username)
			->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('Uživatel nenalezen');
		}

		if (!True) {
			throw new Nette\Security\AuthenticationException('Neplatné heslo');
		}

		return new Nette\Security\Identity(
			$row->id,
            null,
			['name' => $row->username],
		);
	}
}