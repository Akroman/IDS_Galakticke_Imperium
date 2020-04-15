<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Database\Connection;
use Nette\Application\UI\Form;
use Nette;

final class LodePresenter extends BasePresenter
{
    public function __construct(Connection $database)
    {
        parent::__construct($database);
    }

    /*
     * Vykreslení hlavní strany s loděmi
     */
    public function renderDefault($flotila_id)
    {
        $lode = $this->database->query('SELECT lod.lod_id AS LOD_ID, lod.typ AS TYP, planeta.nazev AS NAZEV, lod.poskozeni AS POSKOZENI,
        lod.stity AS STITY, lod.stav_motoru AS STAV_MOTORU, lod.kapacita AS KAPACITA, lod.turety AS TURETY FROM Vesmirna_lod lod
        JOIN Planeta planeta ON planeta.planeta_id = lod.planeta_id WHERE lod.flotila_id = ? ORDER BY lod.lod_id', $flotila_id);

        $velitel = $this->database->fetch('SELECT flotila.velitel_id AS VELITEL, flotila.nazev AS NAZEV FROM Jedi jedi
        JOIN Flotila flotila ON flotila.velitel_id = jedi.jedi_id WHERE flotila.flotila_id = ?', $flotila_id);

        $this->template->lode = $lode;
        $this->template->velitel = $velitel;
        $this->template->flotila_id = $flotila_id;
    }

    /*
     * Formulář pro vytvoření nové lodě
     */
    protected function createComponentRegisterForm()
    {
        $form = new Form;

        $form->addInteger('lod_id', 'ID:')
             ->setRequired('Prosím zvolte ID lodi')
             ->addRule(Form::MIN, 'Zvolte kladné ID prosím', 1);

        $form->addSelect('flotila', 'Flotila:', $this->getPairsFlotilaIdNazev())
             ->setRequired('Prosím zvolte flotilu, do které bude loď spadat');

        $form->addSelect('planeta', 'Planeta výroby:', $this->getPairsPlanetIdNazev())
             ->setRequired('Prosím zvolte planetu výroby');

        $form->addSelect('typ', 'Typ:', [
            '' => '- Vyberte typ lodi -',
            'Bitevni kriznik' => 'Bitevní křižník',
            'Transporter' => 'Transportér']);

        $form->addInteger('poskozeni', 'Míra poškození:')
             ->addCondition(Form::FILLED)
             ->addRule(Form::RANGE, 'Pro poškození zvolte celočíselnou hodnotu v rozsahu od 0 do 100', [0, 100]);

        $form->addInteger('stity', 'Stav štítů:')
             ->addCondition(Form::FILLED)
             ->addRule(Form::RANGE, 'Pro štíty zvolte celočíselnou hodnotu v rozsahu od 0 do 100', [0, 100]);

        $form->addInteger('motory', 'Stav motorů:')
             ->addCondition(Form::FILLED)
             ->addRule(Form::RANGE, 'Pro motory zvolte celočíselnou hodnotu v rozsahu od 0 do 100', [0, 100]);

        $form->addInteger('kapacita', 'Kapacita nákladního prostoru:')
             ->addConditionOn($form['typ'], Form::NOT_EQUAL, 'Transporter')
             ->addRule(Form::BLANK, 'Kapacitu lze zvolit pouze pro typ lodi transportér')
             ->elseCondition()
             ->addRule(Form::MIN, 'Pro kapacitu zvolte kladnou hodnotu', 0);

        $form->addInteger('turety', 'Počet turetů:')
             ->addConditionOn($form['typ'], Form::NOT_EQUAL, 'Bitevni kriznik')
             ->addRule(Form::BLANK, 'Počet turetů lze zvolit pouze pro typ lodi bitevní křižník')
             ->elseCondition()
             ->addRule(Form::MIN, 'Pro početu turetů zvolte kladnou hodnotu', 0);

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
                    $this->database->query('INSERT INTO Vesmirna_lod (lod_id, flotila_id, planeta_id, poskozeni, stity, typ, stav_motoru,
                    kapacita, turety) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', $values->lod_id, $values->flotila, $values->planeta,
                    $values->poskozeni, $values->stity, $values->typ, $values->motory, $values->kapacita, $values->turety);
                    $this->flashMessage('Loď úspěšně vytvořena', 'success');
                    $this->redirect('Lode:', $values->flotila);
               }
               catch (Nette\Database\ConstraintViolationException $exception)
               {
                    $this->flashMessage('Chyba při úkladání lodě', 'error');
               }
               catch (Nette\Database\DriverException $exception)
               {
                    $this->flashMessage('Chyba při úkladání lodě', 'error');
               }
          }
          else
          {
               try
               {
                    $this->database->query('UPDATE Vesmirna_lod SET flotila_id = ?, planeta_id = ?, poskozeni = ?, stity = ?,
                    typ = ?, stav_motoru = ?, kapacita = ?, turety = ? WHERE lod_id = ?', $values->flotila, $values->planeta,
                    $values->poskozeni, $values->stity, $values->typ, $values->motory, $values->kapacita, $values->turety, $values->lod_id);
                    $this->flashMessage('Stav lodi úspešně upraven', 'success');
                    $this->redirect('Lode:', $values->flotila);
               }
               catch (Nette\Database\ConstraintViolationException $exception)
               {
                    $this->flashMessage('Chyba při úkladání lodě', 'error');
               }
               catch (Nette\Database\DriverException $exception)
               {
                    $this->flashMessage('Chyba při úkladání lodě', 'error');
               }
          }
    }

    /* 
     * Zkontroluje, zda je uživatel v roli Palpatine nebo je velitel flotily
     * pokud přidáváme loď přímo ve flotile, tak vloží flotilu do formu
     */
    public function actionRegister($velitel_id = NULL, $flotila_id = NULL)
    {
         if($velitel_id)
         {
               $this->checkUserRoleAndId($velitel_id);
         }
         if($flotila_id)
         {
              $this['registerForm']['flotila']->setDisabled()->setOmitted(false)->setDefaultValue($flotila_id);
         }
    }

    /*
     * Zkontroluje, zda je uživatel v roli Palpatine nebo je velitel flotily a vloží do formu hodnoty z databáze
     */
    public function actionEdit($lod_id, $velitel_id = NULL, $flotila_id = NULL)
    {
          if($velitel_id)
          {
               $this->checkUserRoleAndId($velitel_id);
          }

          $lod = $this->database->fetch('SELECT * FROM Vesmirna_lod WHERE lod_id = ?', $lod_id);

          if(!$lod)
          {
               $this->flashMessage('Tato loď neexistuje', 'error');
               $this->redirect('Lode:', $flotila_id);
          }

          $this['registerForm']->setDefaults([
               'flotila' => $lod->FLOTILA_ID,
               'planeta' => $lod->PLANETA_ID,
               'typ' => $lod->TYP,
               'poskozeni' => $lod->POSKOZENI,
               'stity' => $lod->STITY,
               'motory' => $lod->STAV_MOTORU,
               'kapacita' => $lod->KAPACITA,
               'turety' => $lod->TURETY
          ]);

          $this['registerForm']['lod_id']->setDisabled()->setOmitted(false)->setDefaultValue($lod_id);
    }

    /*
     * Odstraní loď z databáze
     */
    public function actionDelete($lod_id, $velitel_id, $flotila_id)
    {
          $this->checkUserRoleAndId($velitel_id);

          try
          {
               $this->database->query('DELETE FROM Vesmirna_lod WHERE lod_id = ?', $lod_id);
               $this->flashMessage('Smazání proběhlo úspěšně', 'success');
               $this->redirect('Lode:', $flotila_id);
          }
          catch (Nette\Database\DriverException $exception)
          {
               $this->flashMessage('Chyba při mazání lodě', 'error');
               $this->redirect('Lode:', $flotila_id);
          }
          catch (Nette\Database\ConstraintViolationException $exception)
          {
               $this->flashMessage('Chyba při mazání lodě', 'error');
               $this->redirect('Lode:', $flotila_id);
          }
    }
}