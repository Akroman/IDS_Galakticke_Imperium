<?php

namespace App\Authenticator;

use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Nette\Database\Connection;
use Nette\Security\Passwords;
use Nette\Security\IAuthenticator;
use Nette;

class MyAuthenticator implements IAuthenticator
{
	private $database;

	private $passwords;

	public function __construct(Connection $database, Passwords $passwords)
	{
		$this->database = $database;
		$this->passwords = $passwords;
	}

	/*
	 * Ověří zadaný username a heslo a vrátí identitu uživatele obsahující jeho ID, roli, username a cele jmeno
	 */
	public function authenticate(array $credentials): Nette\Security\IIdentity
	{
		[$username, $password] = $credentials;

		$row = $this->database->fetch('SELECT * FROM Jedi WHERE username = ?', $username);

        if (!$row) 
        {
			throw new AuthenticationException('Uživatel nenalezen');
		}

        if (!$this->passwords->verify($password, $row->PASS)) 
        {
			throw new AuthenticationException('Špatné heslo');
		}

		return new Identity($row->JEDI_ID, $row->OPRAVNENI, ['username' => $row->USERNAME, 'name' => $row->JMENO . ' ' . $row->PRIJMENI]);
	}
}