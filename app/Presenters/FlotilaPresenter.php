<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Database\Connection;
use Nette\Application\UI\Form;
use Nette;

final class FlotilaPresenter extends BasePresenter
{
    public function __construct(Connection $database)
    {
        parent::__construct($database);
    }

    /*
     * Vykreslení hlavní strany s flotilami
     */
    public function renderDefault()
    {
        $flotily = $this->database->query('SELECT flotila.flotila_id AS FLOTILA_ID, flotila.nazev AS NAZEV,
        jedi.jmeno AS JMENO, jedi.prijmeni AS PRIJMENI, flotila.pocet_clenu AS POCET_CLENU, flotila.velitel_ID AS VELITEL 
        FROM Flotila flotila JOIN Jedi jedi ON jedi.jedi_id = flotila.velitel_id ORDER BY flotila.flotila_id');

        $this->template->flotily = $flotily;
    }

    /*
     * Zobrazení informací o dané flotile
     */
    public function renderShow($flotila_id)
    {
        $flotila = $this->database->fetch('SELECT flotila.flotila_id AS FLOTILA_ID, flotila.nazev AS NAZEV,
        jedi.jmeno || \' \' || jedi.prijmeni AS JMENO, flotila.pocet_clenu AS POCET_CLENU, flotila.velitel_id AS VELITEL
        FROM Flotila flotila JOIN Jedi jedi ON jedi.jedi_id = flotila.velitel_id WHERE flotila.flotila_id = ?', $flotila_id);

        $planeta = $this->database->fetch('SELECT planeta.nazev AS NAZEV FROM Planeta planeta
        JOIN Flotila flotila ON planeta.planeta_id = flotila.planeta_id WHERE flotila.flotila_id = ?', $flotila_id);

        $pocet_jedi = $this->database->fetch('SELECT COUNT(jedi.jedi_id) AS POCET_JEDI
        FROM Jedi jedi JOIN Flotila flotila ON flotila.flotila_id = jedi.flotila_id WHERE flotila.flotila_id = ?', $flotila_id);

        $lode = $this->database->fetch('SELECT COUNT(lod.lod_id) AS POCET_LODI FROM Vesmirna_lod lod 
        JOIN Flotila flotila ON flotila.flotila_id = lod.flotila_id WHERE flotila.flotila_id = ?', $flotila_id);

        $this->template->flotila = $flotila;
        $this->template->planeta = $planeta;
        $this->template->lode = $lode;
        $this->template->pocet_jedi = $pocet_jedi;
    }

    /*
     * Zobrazení formuláře pro odstranění flotily
     */
    public function renderDelete($flotila_id)
    {
        $nazev = $this->database->fetch('SELECT nazev FROM Flotila WHERE flotila_id = ?', $flotila_id);
        $this->template->nazev = $nazev->NAZEV;
    }

    /*
     * Formulář pro vytvoření nové flotily
     */
    protected function createComponentRegisterForm()
    {
        $form = new Form;

        $planety[''] = '- Vyberte planetu -';
        $planety = array_merge($planety, $this->getPairsPlanetIdNazev());

        $form->addHidden('flotila_id');

        $form->addText('nazev', 'Název:')
             ->setRequired('Prosím zvolte název flotily');

        $form->addSelect('velitel', 'Velitel:', $this->getPairsJediIdNazev())
             ->setRequired('Prosím vyberte velitele flotily');

        $form->addSelect('planeta', 'Planeta:', $planety);

        $form->addInteger('clenove', 'Počet členů flotily:')
             ->setRequired('Prosím zadejte počet členů flotily')
             ->addRule(Form::MIN, 'Pro počet členů zvolte kladné číslo prosím', 0);

        $form->addSubmit('odeslat', 'Uložit');

        $form->onSuccess[] = [$this, 'registerFormSucceeded'];

        return $form;
    }

    /*
     * Callback při submitu register formu, vloží zadaná data do databáze
     */
    public function registerFormSucceeded($form, $values)
    {
        $values = $form->getValues();

        if($this->getAction() === 'register')
        {
            try
            {
                $this->database->query('INSERT INTO Flotila (planeta_id, velitel_id, nazev, pocet_clenu)
                VALUES (?, ?, ?, ?)', $values->planeta, $values->velitel, $values->nazev, $values->clenove);
                $this->database->query('UPDATE Jedi SET flotila_id = ? WHERE jedi_id = ?', $values->flotila_id, $values->velitel);

                $this->flashMessage('Flotila úspěšně vytvořena', 'success');
                $this->redirect('Flotila:');
            }
            catch (Nette\Database\ConstraintViolationException $exception)
            {
                $this->flashMessage('Chyba při ukladání flotily, název je pravděpodobně zabraný nebo zadný velitel již velí některé flotile', 'error');
            }
            catch (Nette\Database\DriverException $exception)
            {
                $this->flashMessage('Chyba při ukladání flotily', 'error');
            }
        }
        else
        {
            try
            {
                $this->database->query('UPDATE Jedi SET flotila_id = ? WHERE jedi_id = ?', $values->flotila_id, $values->velitel);
                $this->database->query('UPDATE Flotila SET planeta_id = ?, velitel_id = ?, nazev = ?, pocet_clenu = ?
                WHERE flotila_id = ?', $values->planeta, $values->velitel, $values->nazev, $values->clenove, $values->flotila_id);

                $this->flashMessage('Úprava flotily proběhla úspěšně', 'success');
                $this->redirect('Flotila:');
            }
            catch (Nette\Database\ConstraintViolationException $exception)
            {
                $this->flashMessage('Chyba při ukladání flotily, název je pravděpodobně zabraný nebo zadný velitel již velí některé flotile', 'error');
            }
            catch (Nette\Database\DriverException $exception)
            {
                $this->flashMessage('Chyba při ukladání flotily', 'error');
            }
        }
    }

    /*
     * Formulář pro odstranění flotily, stará se o přesun lodí z flotily
     */
    protected function createComponentDeleteForm()
    {
        $form = new Form;

        $form->addHidden('flotila_id');

        $form->addSelect('flotila', 'Vyberte prosím, kam přesunout lodě z vymazávané flotily', $this->getPairsFlotilaIdNazev())
             ->addRule(Form::NOT_EQUAL, 'Vyberte prosím jinou flotilu, než která je vymazávána', $form['flotila_id']);

        $form->addSubmit('odstranit', 'Odstranit');

        $form->onSuccess[] = [$this, 'deleteFormSucceeded'];

        return $form;
    }

    protected function createComponentSearchForm()
    {
        $form = new Form;

        $form->addText('flotila', 'Vyhledat flotilu podle ID:');

        $form->addSubmit('odeslat', 'Vyhledat');

        $form->onSuccess[] = [$this, 'searchFormSucceeded'];

        return $form;
    }

    public function searchFormSucceeded($form, $values)
    {
        $values = $form->getValues();

        $flotila = $this->database->fetch('SELECT flotila_id FROM Flotila WHERE flotila_id = ?', $values->flotila);

        if(!$flotila)
        {
            $this->flashMessage('Flotila nenalezena', 'error');
        }
        else
        {
            $this->redirect('Flotila:show', $values->flotila);
        }
    }

    /*
     * Callback při submitu delete formu, odstraní flotilu z databáze a přesune její lodě do zvolené flotily
     */
    public function deleteFormSucceeded($form, $values)
    {
        $values = $form->getValues();

        try
        {
            $this->database->query('UPDATE Vesmirna_lod SET flotila_id = ? WHERE flotila_id = ?', $values->flotila, $values->flotila_id);
            $this->database->query('DELETE FROM Flotila WHERE flotila_id = ?', $values->flotila_id);
            $this->flashMessage('Flotila úspěšně smazána', 'success');
            $this->redirect('Flotila:');
        }
        catch (Nette\Database\DriverException $exception)
        {
            $this->flashMessage('Chyba při mazání Flotily', 'error');
        }
        catch (Nette\Database\ConstraintViolationException $exception)
        {
            $this->flashMessage('Chyba při mazání Flotily', 'error');
        }
    }

    public function actionRegister()
    {
        $this->checkUserRole();
    }

    /*
     * Při editaci flotily vloží do formu hodnoty z databáze
     */
    public function actionEdit($flotila_id)
    {
        $this->checkUserRoleAndId($this->getVelitelId($flotila_id));
        $flotila = $this->database->fetch('SELECT * FROM Flotila WHERE flotila_id = ?', $flotila_id);

        if(!$flotila)
        {
            $this->flashMessage('Tato flotila neexistuje', 'error');
            $this->redirect('Flotila:');
        }

        $this->template->nazev = $flotila->NAZEV;

        $this['registerForm']->setDefaults([
            'flotila_id' => $flotila->FLOTILA_ID,
            'nazev' => $flotila->NAZEV,
            'velitel' => $flotila->VELITEL_ID,
            'planeta' => $flotila->PLANETA_ID,
            'clenove' => $flotila->POCET_CLENU
        ]);

        if(!$this->getUser()->isInRole('Palpatine'))
        {
            $this['registerForm']['velitel']->setDisabled()->setOmitted(false)->setValue($flotila->VELITEL_ID);
        }
    }

    /*
     * Nastaví v delete formu skrytou hodnotu id flotily
     */
    public function actionDelete($flotila_id)
    {
        $this->checkUserRole();

        $this['deleteForm']['flotila_id']->setValue($flotila_id);
    }
}