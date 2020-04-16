<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Database\Connection;
use Nette\Application\UI\Form;
use Nette;

final class PlanetyPresenter extends BasePresenter
{
    public function __construct(Connection $database)
    {
        parent::__construct($database);
    }

    public function renderDefault($system_id)
    {
        $planety = $this->database->query('SELECT * FROM Planeta WHERE system_id = ? ORDER BY planeta_id', $system_id);
        $system = $this->database->fetch('SELECT nazev FROM Planetarni_system WHERE system_id = ?', $system_id);

        $this->template->planety = $planety;
        $this->template->system = $system;
    }

    protected function createRegisterComponent()
    {
        $form = new Form;



        $form->onSuccess[] = [$this, 'registerFormSucceeded'];

        return $form;
    }

    public function registerFormSucceeded($form, $values)
    {

    }

    public function actionRegister()
    {
        $this->checkUserRole();
    }

    public function actionEdit($planeta_id, $system_id)
    {
        $this->checkUserRole();
    }

    public function actionDelete($planeta_id, $system_id)
    {
        $this->checkUserRole();

        if(!$this->database->fetch('SELECT * FROM Planeta WHERE planeta_id = ?', $planeta_id))
        {
            $this->flashMessage('Tato planeta neexistuje', 'error');
            $this->redirect('Planety:', $system_id);
        }

        try
        {
            $this->database->query('DELETE FROM Planeta WHERE planeta_id = ?', $planeta_id);
            $this->flashMessage('Planeta úspěšně smazána', 'success');
            $this->redirect('Planety:', $system_id);
        }
        catch (Nette\Database\DriverException $exception)
        {
            $this->flashMessage('Chyba při mazání planety', 'error');
            $this->redirect('Planety:', $system_id);
        }
        catch (Nette\Database\ConstraintViolationException $exception)
        {
            $this->flashMessage('Nelze smazat planetu, pravděpodobně se na ní nachází flotila nebo je rodnou planetou Jedi', 'error');
            $this->redirect('Planety:', $system_id);
        }
    }
}