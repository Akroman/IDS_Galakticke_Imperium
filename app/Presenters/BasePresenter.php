<?php

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Database\Connection;
use Nette;

class BasePresenter extends Presenter
{
    /** @var Nette\Database\Connection */
    protected $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    /*
     * Vrátí datum narození jednoho nebo všech Jedi
     */
    protected function getJediBirthDate($jedi_id = NULL)
    {
        return $jedi_id 
        ? $this->database->fetch('SELECT TO_CHAR( dat_narozeni, \'DD.MM.YYYY\' ) AS NAROZENI FROM Jedi WHERE jedi_id = ?', $jedi_id)
        : $this->database->query('SELECT TO_CHAR( dat_narozeni, \'DD.MM.YYYY\' ) AS NAROZENI FROM Jedi');
    }

    /*
     * Vrátí asociativní pole s ID a názvy planet
     */
    protected function getPairsPlanetIdNazev()
    {
        return $this->database->fetchPairs('SELECT planeta_id, nazev FROM Planeta');
    }

    /*
     * Vrátí asociativní pole s ID a názvy flotil
     */
    protected function getPairsFlotilaIdNazev()
    {
        return $this->database->fetchPairs('SELECT flotila_id, nazev FROM Flotila');
    }

    /*
     * Vrátí asociativní pole ID a celým jménem Jedi
     */
    protected function getPairsJediIdNazev()
    {
        return $this->database->fetchPairs('SELECT jedi_id, jmeno || \' \' || prijmeni AS CELE_JMENO FROM Jedi');
    }

    protected function getPairsSystemIdNazev()
    {
        return $this->database->fetchPairs('SELECT system_id, nazev FROM Planetarni_system');
    }

    /*
     * Zkontroluje, zda je uživatel v roli Palpatine, jinak ho přesměruje na hlavní stranu
     */
    protected function checkUserRole()
    {
        if(!$this->getUser()->isInRole('Palpatine'))
        {
            $this->flashMessage('Na tuto akci nemáte dostatečná oprávnění', 'error');
            $this->redirect('Homepage:');
        }
    }

    /*
     * Zkontroluje, zda je uživatel přihlášen, jinak ho přesměruje na hlavní stranu
     */
    protected function checkUserLoggedIn()
    {
        if(!$this->getUser()->isLoggedIn())
        {
            $this->flashMessage('Na tuto akci nemáte dostatečná oprávnění', 'error');
            $this->redirect('Homepage:');
        }
    }

    /*
     * Zkontroluje, zda je uživatel v roli Palpatine nebo má požadované ID, jinak ho přesměruje na hlavní stranu
     */
    protected function checkUserRoleAndId($user_id)
    {
        if($this->getUser()->isInRole('Palpatine') || $this->getUser()->getId() == $user_id)
        {
            return;
        }
        else
        {
            $this->flashMessage('Na tuto akci nemáte dostatečná oprávnění', 'error');
            $this->redirect('Homepage:');
        }
    }

    /*
     * Vrátí ID velitele z dané flotily
     */
    protected function getVelitelId($flotila_id)
    {
        return $this->database->fetch('SELECT flotila.velitel_id AS VELITEL FROM Jedi jedi
        JOIN Flotila flotila ON flotila.velitel_id = jedi.jedi_id WHERE flotila.flotila_id = ?', $flotila_id)->VELITEL;
    }
}