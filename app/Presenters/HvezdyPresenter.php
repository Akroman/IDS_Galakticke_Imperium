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
        $system = $this->database->fetch('SELECT * FROM Planetarni_system WHERE system_id = ?', $system_id);

        $this->template->hvezdy = $hvezdy;
        $this->template->system = $system;
    }

    protected function createComponentRegisterForm()
    {
        $form = new Form;

        $form->addHidden('hvezda_id');

        $form->addText('nazev', 'Název:')
             ->setRequired('Prosím zvolte název hvězdy');

        $form->addSelect('system', 'Planetární systém:', $this->getPairsSystemIdNazev())
             ->setRequired('Prosím zvolte planetární systém hvězdy');

        $form->addText('typ', 'Typ:');

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
                $this->database->query('INSERT INTO Hvezda system_id, nazev, typ VALUES (?, ?, ?)',
                $values->system, $values->nazev, $values->typ);
                $this->flashMessage('Hvězda úspěšně vytvořena', 'success');
                $this->redirect('Hvezdy:', $values->system);
            }
            catch (Nette\Database\ConstraintViolationException $exception)
            {
                $this->flashMessage('Chyba při ukladání hvězdy', 'error');
            }
            catch (Nette\Database\DriverException $exception)
            {
                $this->flashMessage('Chyba při ukladání hvězdy', 'error');
            }
        }
        else
        {
            try
            {
                $this->database->query('UPDATE Hvezda SET system_id = ?, nazev = ?, typ = ? WHERE hvezda_id = ?',
                $values->system, $values->nazev, $values->typ, $values->hvezda_id);
                $this->flashMessage('Hvězda úspěšně editována', 'success');
                $this->redirect('Hvezdy:', $values->system);
            }
            catch (Nette\Database\ConstraintViolationException $exception)
            {
                $this->flashMessage('Chyba při ukladání hvězdy', 'error');
            }
            catch (Nette\Database\DriverException $exception)
            {
                $this->flashMessage('Chyba při ukladání hvězdy', 'error');
            }
        }
    }

    public function actionRegister($system_id = NULL)
    {
        $this->checkUserRole();

        if($system_id)
        {
            $this['registerForm']['system']->setDisabled()->setOmitted(false)->setDefaultValue($system_id);
        }
    }

    public function actionEdit($hvezda_id, $system_id = NULL)
    {
        $this->checkUserRole();

        $hvezda = $this->database->fetch('SELECT * FROM Hvezda WHERE hvezda_id = ?', $hvezda_id);

        if(!$hvezda)
        {
            $this->flashMessage('Tato hvězda neexistuje', 'error');
            $this->redirect('Hvezdy:', $system_id);
        }

        $this->template->hvezda = $hvezda->NAZEV;

        $this['registerForm']->setDefaults([
            'hvezda_id' => $hvezda->HVEZDA_ID,
            'nazev' => $hvezda->NAZEV,
            'typ' => $hvezda->TYP,
            'system' => $hvezda->SYSTEM_ID
        ]);
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