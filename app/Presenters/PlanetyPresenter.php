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
        $system = $this->database->fetch('SELECT * FROM Planetarni_system WHERE system_id = ?', $system_id);

        $this->template->planety = $planety;
        $this->template->system = $system;
    }

    protected function createComponentRegisterForm()
    {
        $form = new Form;

        $form->addHidden('planeta_id');

        $form->addText('nazev', 'Název:')
             ->setRequired('Prosím zvolte název planety');

        $form->addSelect('system', 'Planetární systém:', $this->getPairsSystemIdNazev())
             ->setRequired('Prosím zvolte planetární systém planety');

        $form->addText('typ', 'Typ:');

        $form->addInteger('obyvatele', 'Počet obyvatel:')
             ->addCondition(Form::FILLED)
             ->addRule(Form::MIN, 'Pro počet obyvatel zvolte kladné číslo', 1);

        $form->addInteger('vzdalenost', 'Vzdálenost od slunce:')
             ->addCondition(Form::FILLED)
             ->addRule(Form::MIN, 'Pro vzdálenost od slunce zvolte kladnou hodnotu', 1);

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
                $this->database->query('INSERT INTO Planeta system_id, nazev, typ, vzdalenost_slunce, pocet_obyvatel
                VALUES (?, ?, ?, ?, ?)', $values->system, $values->nazev, $values->typ, $values->vzdalenost, $values->obyvatele);
                $this->flashMessage('Planeta úspěšně vytvořena', 'success');
                $this->redirect('Planety:', $values->system);
            }
            catch (Nette\Database\ConstraintViolationException $exception)
            {
                $this->flashMessage('Chyba při ukladání planety', 'error');
            }
            catch (Nette\Database\DriverException $exception)
            {
                $this->flashMessage('Chyba při ukladání planety', 'error');
            }
        }
        else
        {
            try
            {
                $this->database->query('UPDATE Planeta SET system_id = ?, nazev = ?, typ = ?, vzdalenost_slunce = ?, pocet_obyvatel = ?
                WHERE planeta_id = ?', $values->system, $values->nazev, $values->typ, $values->vzdalenost, $values->obyvatele, $values->planeta_id);
                $this->flashMessage('Planeta úspěšně upravena', 'success');
                $this->redirect('Planety:', $values->system);
            }
            catch (Nette\Database\ConstraintViolationException $exception)
            {
                $this->flashMessage('Chyba při ukladání planety', 'error');
            }
            catch (Nette\Database\DriverException $exception)
            {
                $this->flashMessage('Chyba při ukladání planety', 'error');
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

    public function actionEdit($planeta_id, $system_id = NULL)
    {
        $this->checkUserRole();

        $planeta = $this->database->fetch('SELECT * FROM Planeta WHERE planeta_id = ?', $planeta_id);

        if(!$planeta)
        {
            $this->flashMessage('Tato planeta neexistuje', 'error');
            $this->redirect('Planety:', $system_id);
        }

        $this->template->planeta = $planeta->NAZEV;

        $this['registerForm']->setDefaults([
            'planeta_id' => $planeta->PLANETA_ID,
            'system' => $planeta->SYSTEM_ID,
            'nazev' => $planeta->NAZEV,
            'typ' => $planeta->TYP,
            'obyvatele' => $planeta->POCET_OBYVATEL,
            'vzdalenost' => $planeta->VZDALENOST_SLUNCE
        ]);
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