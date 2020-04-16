<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Database\Connection;
use Nette\Application\UI\Form;
use Nette;

final class PlanetarniSystemPresenter extends BasePresenter
{
    public function __construct(Connection $database)
    {
        parent::__construct($database);
    }

    public function renderDefault()
    {
        $planetarni_systemy = $this->database->query('SELECT sys.system_id AS ID, sys.nazev AS NAZEV,
        COUNT(DISTINCT planeta.planeta_id) AS POCET_PLANET, COUNT(DISTINCT hvezda.hvezda_id) AS POCET_HVEZD
        FROM Planetarni_system sys JOIN Planeta planeta ON planeta.system_id = sys.system_id
        JOIN Hvezda hvezda ON hvezda.system_id = sys.system_id GROUP BY sys.system_id, sys.nazev ORDER BY sys.system_id');

        $this->template->planetarni_systemy = $planetarni_systemy;
    }

    protected function createComponentRegisterForm()
    {
        $form = new Form;

        $form->addText('nazev', 'Název:')
             ->setRequired('Prosím zvolte název systému');

        $form->addSubmit('odeslat', 'Uložit');
        
        $form->onSuccess[] = [$this, 'registerFormSucceeded'];

        return $form;
    }

    public function registerFormSucceeded($form, $values)
    {
        $values = $form->getValues();

        if($this->getAction() === 'register')
        {
            try
            {
                $this->database->query('INSERT INTO Planetarni_system Nazev VALUES ?', $values->nazev);
                $this->flashMessage('Planetární systém úspěšně vytvořen', 'success');
                $this->redirect('PlanetarniSystem:');
            }
            catch (Nette\Database\DriverException $exception)
            {
                $this->flashMessage('Chyba při ukládání planetárního systému', 'error');
            }
            catch (Nette\Database\ConstraintViolationException $exception)
            {
                $this->flashMessage('Chyba při ukládání planetárního systému', 'error');
            }
        }
        else
        {
            try
            {
                $this->database->query('UPDATE Planetarni_system SET Nazev = ? WHERE system_id = ?', $values->nazev);
                $this->flashMessage('Planetární systém úspěšně upraven', 'success');
                $this->redirect('PlanetarniSystem:');
            }
            catch (Nette\Database\DriverException $exception)
            {
                $this->flashMessage('Chyba při ukládání planetárního systému', 'error');
            }
            catch (Nette\Database\ConstraintViolationException $exception)
            {
                $this->flashMessage('Chyba při ukládání planetárního systému', 'error');
            }
        }
    }

    public function actionRegister()
    {
        $this->checkUserRole();
    }

    public function actionEdit($system_id)
    {
        $this->checkUserRole();

        $system = $this->database->fetch('SELECT * FROM Planetarni_system WHERE system_id = ?', $system_id);

        if(!$system)
        {
            $this->flashMessage('Planetární systém neexistuje', 'error');
            $this->redirect('PlanetarniSystem:');
        }


    }

    public function actionDelete($system_id)
    {
        $this->checkUserRole();

        if(!$this->database->fetch('SELECT * FROM Planetarni_system WHERE system_id = ?, $system_id'))
        {
            $this->flashMessage('Planetární systém neexistuje', 'error');
            $this->redirect('PlanetarniSystem:');
        }

        try
        {
            $this->database->query('DELETE FROM Planetarni_system WHERE system_id = ?', $system_id);
            $this->flashMessage('Planetární systém úspěšně smazán');
            $this->redirect('PlanetarniSystem:');
        }
        catch (Nette\Database\DriverException $exception)
        {
            $this->flashMessage('Chyba při mazání planetárního systému', 'error');
            $this->redirect('PlanetarniSystem:');
        }
        catch (Nette\Database\ConstraintViolationException $exception)
        {
            $this->flashMessage('Nelze smazat planetární systém, pravděpodobně obsahuje nějaké hvězdy či planety', 'error');
            $this->redirect('PlanetarniSystem:');
        }
    }
}