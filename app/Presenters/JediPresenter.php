<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Database\Connection;
use Nette\Application\UI\Form;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;
use Nette;

final class JediPresenter extends BasePresenter
{
    private $passwords;

    /*
     * Pole pro výběr rasy
     */
    const RASY = [
        '' => '- Zvolte rasu -',
        'Nautolan' => 'Nautolan',
        'Clovek' => 'Člověk',
        'Cerean' => 'Cerean',
        'Kel Dor' => 'Kel Dor',
        'Togruta' => 'Togruta',
        'Mirialan' => 'Mirialan'
    ];

    /*
     * Pole pro výběr barvy mečů
     */
    const BARVY_MECU = [
        '' => '- Vyberte barvu meče -',
        'Cervena' => 'Červená',
        'Zelena' => 'Zelená',
        'Modra' => 'Modrá',
        'Fialova' => 'Fialová',
        'Zluta' => 'Žlutá',
        'Bila' => 'Bílá',
        'Cerna' => 'Černá',
        'Oranzova' => 'Oranžová'
    ];

    public function __construct(Connection $database)
    {
        parent::__construct($database);
        $this->passwords = new Passwords();
    }

    /*
     * Zobrazení tabulky všech Jedi
     */
    public function renderDefault()
    {
        $this->checkUserLoggedIn();

        $request = $this->database->query('SELECT TO_CHAR( dat_narozeni, \'DD.MM.YYYY\' ) AS NAROZENI, jedi_id, jmeno, prijmeni, rasa
        FROM Jedi ORDER BY jedi_id');
        $this->template->requests = $request;
    }

    /*
     * Zobrazení profilu jednotlivých Jedi
     */
    public function renderShow($jedi_id)
    {
        $this->checkUserLoggedIn();

        $jedi = $this->database->fetch('SELECT * FROM Jedi WHERE jedi_id = ?', $jedi_id);

        if(!$jedi)
        {
            $this->flashMessage('Tento Jedi neexistuje', 'error');
            $this->redirect('Jedi:');
        }

        $narozeni = $this->getJediBirthDate($jedi_id);

        $planeta = $this->database->fetch('SELECT planeta.nazev AS NAZEV FROM Planeta planeta JOIN Jedi jedi ON
        jedi.planeta_id = planeta.planeta_id WHERE jedi.jedi_id = ?', $jedi_id);

        $flotila = $this->database->fetch('SELECT flotila.nazev AS NAZEV FROM Flotila flotila JOIN Jedi jedi ON
        jedi.flotila_id = flotila.flotila_id WHERE jedi.jedi_id = ?', $jedi_id);

        $this->template->jedi = $jedi;
        $this->template->narozeni = $narozeni;
        $this->template->planeta = $planeta;
        $this->template->flotila = $flotila;
    }    

    /*
     * Vytvoří komponentu pro vyhledání Jedi
     */
    protected function createComponentSearchForm()
    {
        $form = new Form;

        $form->addText('jedi', 'Vyhledat Jedi podle ID:');

        $form->addSubmit('odeslat', 'Vyhledat');

        $form->onSuccess[] = [$this, 'searchFormSucceeded'];

        return $form;
    }

    /*
     * Pokusí se vyhledat Jedi podle zadaného ID
     */
    public function searchFormSucceeded($form, $values)
    {
        $values = $form->getValues();

        $jedi = $this->database->fetch('SELECT jedi_id FROM Jedi WHERE jedi_id = ?', $values->jedi);

        if(!$jedi)
        {
            $this->flashMessage('Jedi nenalezen', 'error');
        }
        else
        {
            $this->redirect('Jedi:show', $values->jedi);
        }
    }

    /*
     * Formulář pro registraci nebo úpravu Jedi
     */
    protected function createComponentRegisterForm()
    {
        $form = new Form;

        $form->addInteger('jedi_id', 'ID Jedi:')
             ->setRequired('Prosím zadejte ID Jedi')
             ->addRule(Form::MIN, 'Zvolte kladné ID prosím', 1);

        $form->addText('jmeno', 'Jméno:')
             ->setRequired('Prosím zadejte jméno Jedi');

        $form->addText('prijmeni', 'Příjmení:')
             ->setRequired('Prosím zadejte příjmení Jedi');

        $form->addSelect('planeta_id', 'Planeta původu:', $this->getPairsPlanetIdNazev())
             ->setRequired('Prosím zadejte planetu původu');

        $form->addSelect('flotila_id', 'Flotila:', $this->getPairsFlotilaIdNazev());

        $form->addSelect('rasa', 'Rasa:', self::RASY);

        $form->addInteger('midichlorian', 'Množství midichlorianů')
             ->addRule(Form::MIN, 'Pro množství midichlorianů zvolte kladnou hodnotu prosím', 0);

        $form->addSelect('barva_mece', 'Barva meče:', self::BARVY_MECU);

        $form->addText('narozeni', 'Datum narození:')
             ->addRule(Form::PATTERN, 'Prosím zadejte datum ve formátu DD-MON-YYYY', '[0-3][0-9]-[A-Z]{3}-[0-9]*')
             ->setDefaultValue('DD-MON-YYYY');

        $form->addSelect('padawan', 'Jedi je padawanem:', [0 => 'Ne', 1 => 'Ano']);

        $form->addSelect('opravneni', 'Oprávnění Jedi:', ['Jedi' => 'Jedi', 'Palpatine' => 'Palpatine'])
             ->setRequired('Prosím zvolte oprávnění Jedi');

        $form->addText('username', 'Uživatelské jméno:')
             ->setRequired('Prosím vyplňte uživatelské jméno');

        $form->addPassword('pass', 'Heslo:')
             ->setRequired('Prosím vyberte si heslo');

        $form->addSubmit('submit', 'Uložit');

        $form->onSuccess[] = [$this, 'registerFormSucceeded'];

        return $form;
    }

    /*
     * Callback při submitu register formu, zahashuje heslo a vloží všechna data do databáze
     */
    public function registerFormSucceeded($form, $values)
    {
        $values = $form->getValues();
        $values->pass = $this->passwords->hash($values->pass);

        if($this->getAction() === 'register')
        {
            try
            {
                $this->database->query('INSERT INTO Jedi (jedi_id, planeta_id, flotila_id, jmeno, prijmeni, rasa,
                mnozstvi_chlorianu, barva_mece, dat_narozeni, je_padawan, username, pass, opravneni)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                intval($values->jedi_id), $values->planeta_id, $values->flotila_id, $values->jmeno, $values->prijmeni, $values->rasa, 
                intval($values->midichlorian), $values->barva_mece, $values->narozeni, $values->padawan, $values->username,
                $values->pass, $values->opravneni
                );

                $this->flashMessage('Jedi byl úspěšně přídán do systému', 'success');
                $this->redirect('Jedi:');
            }
            catch (Nette\Database\ConstraintViolationException $exception)
            {
                $this->flashMessage('Chyba při úkladání Jedi', 'error');
            }
            catch (Nette\Database\DriverException $exception)
            {
                $this->flashMessage('Chyba při úkladání Jedi', 'error');
            }
        }
        else
        {
            try
            {
                $this->database->query('UPDATE Jedi SET planeta_id = ?, flotila_id = ?, jmeno = ?, prijmeni = ?, rasa = ?,
                mnozstvi_chlorianu = ?, barva_mece = ?, dat_narozeni = ?, je_padawan = ?, username = ?, pass = ?, opravneni = ?
                WHERE jedi_id = ?', $values->planeta_id, $values->flotila_id, $values->jmeno, $values->prijmeni, $values->rasa,
                intval($values->midichlorian), $values->barva_mece, $values->narozeni, $values->padawan, $values->username,
                $values->pass, $values->opravneni, intval($values->jedi_id)
                );

                $this->flashMessage('Úprava profilu Jedi proběhla úspěšně', 'success');
                $this->redirect('Jedi:');
            }
            catch (Nette\Database\ConstraintViolationException $exception)
            {
                $this->flashMessage('Chyba při úkladání Jedi', 'error');
            }
            catch (Nette\Database\DriverException $exception)
            {
                $this->flashMessage('Chyba při úkladání Jedi', 'error');
            }
        }
    }

    /*
     * Při editaci Jedi vloží do formu hodnoty z databáze
     */
    public function actionEdit($jedi_id)
    {
        $this->checkUserRoleAndId($jedi_id);
        $jedi = $this->database->fetch('SELECT * FROM Jedi WHERE jedi_id = ?', $jedi_id);

        if(!$jedi)
        {
            $this->flashMessage('Tento Jedi neexistuje', 'error');
            $this->redirect('Jedi:');
        }

        $this->template->jmeno = $jedi->JMENO . ' ' . $jedi->PRIJMENI;

        $narozeni = $this->database->fetch('SELECT TO_CHAR( dat_narozeni, \'DD-MON-YYYY\' ) AS NAROZENI 
        FROM Jedi WHERE jedi_id = ?', $jedi_id);

        $this['registerForm']->setDefaults([
            'jedi_id' => $jedi->JEDI_ID,
            'jmeno' => $jedi->JMENO,
            'prijmeni' => $jedi->PRIJMENI,
            'planeta_id' => $jedi->PLANETA_ID,
            'flotila_id' => $jedi->FLOTILA_ID,
            'rasa' => $jedi->RASA,
            'midichlorian' => $jedi->MNOZSTVI_CHLORIANU,
            'barva_mece' => $jedi->BARVA_MECE,
            'narozeni' => $narozeni->NAROZENI,
            'padawan' => $jedi->JE_PADAWAN,
            'username' => $jedi->USERNAME,
            'opravneni' => $jedi->OPRAVNENI
            ]);

        $this['registerForm']['jedi_id']->setDisabled()->setOmitted(false)->setValue($jedi->JEDI_ID);
    }

    public function actionRegister()
    {
        $this->checkUserRole();
    }

    /*
     * Obstarává odstranění Jedi
     */
    public function actionDelete($jedi_id)
    {
        $this->checkUserRole();

        try
        {
            $this->database->query('DELETE FROM Jedi WHERE jedi_id = ?', $jedi_id);
            $this->flashMessage('Smazání proběhlo úspěšně', 'success');
            $this->redirect('Jedi:');
        }
        catch (Nette\Database\DriverException $exception)
        {
            $this->flashMessage('Chyba při mazání Jedi', 'error');
            $this->redirect('Jedi:');
        }
        catch (Nette\Database\ConstraintViolationException $exception)
        {
            $this->flashMessage('Chyba při mazání Jedi', 'error');
            $this->redirect('Jedi:');
        }
    }
}