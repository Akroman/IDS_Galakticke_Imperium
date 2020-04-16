<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Database\Connection;
use Nette\Application\UI\Form;
use Nette;

final class HvezdyPresenter extends BasePresenter
{
    public function __construct(Connection $database)
    {
        parent::__construct($database);
    }

    public function renderDefault($system_id)
    {
        $hvezdy = $this->database->query('SELECT * FROM Hvezda WHERE system_id = ? ORDER BY hvezda_id', $system_id);
        $system = $this->database->fetch('SELECT Nazev FROM Planetarni_system WHERE system_id = ?', $system_id);

        $this->template->hvezdy = $hvezdy;
        $this->template->system = $system;
    }

    protected function createComponentRegisterForm()
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

    public function actionEdit($hvezda_id, $system_id)
    {
        $this->checkUserRole();
    }

    public function actionDelete($hvezda_id, $system_id)
    {
        $this->checkUserRole();

        if(!$this->database->fetch('SELECT * FROM Hvezda WHERE hvezda_id = ?', $hvezda_id))
        {
            $this->flashMessage('Tato hvězda neexistuje', 'error');
            $this->redirect('Hvezdy:', $system_id);
        }

        try
        {
            $this->database->query('DELETE FROM Hvezda WHERE hvezda_id = ?', $hvezda_id);
            $this->flashMessage('Hvězda úspěšně smazána', 'success');
            $this->redirect('Hvezdy:', $system_id);
        }
        catch (Nette\Database\DriverException $exception)
        {
            $this->flashMessage('Chyba při mazání hvězdy', 'error');
            $this->redirect('Hvezdy:', $system_id);
        }
        catch (Nette\Database\ConstraintViolationException $exception)
        {
            $this->flashMessage('Chyba při mazání hvězdy', 'error');
            $this->redirect('Hvezdy:', $system_id);
        }
    }
}