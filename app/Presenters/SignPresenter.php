<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use App\Authenticator\MyAuthenticator;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Nette\Database\Connection;
use Nette\Security\Passwords;
use Nette\Application\UI\Form;
use Nette;

final class SignPresenter extends BasePresenter
{
    private $authenticator;

    private $passwords;

    public function __construct(Connection $database)
    {
        parent::__construct($database);
        $this->passwords = new Passwords();
        $this->authenticator = new MyAuthenticator($this->database, $this->passwords);
    }

    /*
     * Vytvoří přihlašovací formulář
     */
    protected function createComponentSignInForm()
    {
        $form = new Form;

        $form->addText('username', 'Uživatelské jméno:')
             ->setRequired('Proísm vyplňte uživatelské jméno');

        $form->addPassword('password', 'Heslo:')
             ->setRequired('Prosím vyplňte heslo');

        $form->addSubmit('submit', 'Přihlásit');

        $form->onSuccess[] = [$this, 'signInFormSucceeded'];

        return $form;
    }

    /*
     * Pokusí se přihlásit uživatele pomocí vytvořeného authenticatoru
     */
    public function signInFormSucceeded($form, $values)
    {
        $values = $form->getValues();
        $user = $this->getUser();
        $user->setAuthenticator($this->authenticator);
        try
        {
            $user->login($values->username, $values->password);
            $this->flashMessage('Přihlášení proběhlo úspěšně', 'success');
            $this->redirect('Homepage:');
        }
        catch (AuthenticationException $exception)
        {
            $this->flashMessage($exception->getMessage(), 'error');
        }
    }

    /*
     * Odhlásí uživatele
     */
    public function actionOut(): void
    {
        if($this->getUser()->isLoggedIn())
        {
            $this->getUser()->logout();
            $this->flashMessage('Byl jste odhlášen.', 'success');
            $this->redirect('Homepage:');
        }
    }
}