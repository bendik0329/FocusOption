<?php
abstract class Api {

    abstract function createLead($email,$firstName,$lastName,$phone,$language,$country,$btag, $additioanal = null);
    abstract function createCustomer($email,$password,$firstName,$lastName,$currency,$accountType,$phone,$language,$country,$btag, $additioanal = null);

    abstract function getLeads($param);

    abstract function getFTD($param);

    abstract function getTrades($param);

    abstract function getTransactions($param);
    
    abstract function getAutologin($param);
}
