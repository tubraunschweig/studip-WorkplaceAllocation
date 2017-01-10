<?php

/*
 *  -----------
 *	Connector-Script fuer die Feiertags-API http://feiertage.jarmedia.de
 *	Autor: Julian Richter, JAR Media GmbH
 *  Erstellt: 28.04.2014
 *
 *
 *  Verwendung:
 *	$connector = LPLib_Feiertage_Connector::getInstance();
 *	$is_feiertag = $connector->isFeiertagInLand('2014-01-01', LPLib_Feiertage_Connector::LAND_NORDRHEINWESTPHALEN);
 *	$feiertage_2012_in_bayern = $connector->getFeiertageVonLand(2012, LPLib_Feiertage_Connector::LAND_BAYERN);
 *
 *
 */

class LPLib_Feiertage_Connector
{
    const BASE_URL = 'http://feiertage.jarmedia.de/api/';

    const LAND_BADENWUERTEMBERG = 'BW';
    const LAND_BAYERN = 'BY';
    const LAND_BERLIN = 'BE';
    const LAND_BRANDENBURG = 'BB';
    const LAND_BREMEN = 'HB';
    const LAND_HAMBURG = 'HH';
    const LAND_HESSEN = 'HE';
    const LAND_MECKLENBURGVORPOMMERN = 'MV';
    const LAND_NIEDERSACHSEN = 'NI';
    const LAND_NORDRHEINWESTPHALEN = 'NW';
    const LAND_RHEINLANDPFALZ = 'RP';
    const LAND_SAARLAND = 'SL';
    const LAND_SACHSEN = 'SN';
    const LAND_SACHSENANHALT = 'ST';
    const LAND_SCHLESWIGHOLSTEIN = 'SH';
    const LAND_THUERINGEN = 'TH';

    private static $cache = array();
    private static $itsInstance = null;

    private function __construct(){}

    public static function getInstance()
    {
        if(self::$itsInstance == null)
            self::$itsInstance = new LPLib_Feiertage_Connector();

        return self::$itsInstance;
    }

    public function getFeiertageVonLand($jahr,$land)
    {
        if(isset(self::$cache['c0-'.$jahr.$land]))
            return self::$cache['c0-'.$jahr.$land];

        $result = $this->_file_get_contents_t_curl(LPLib_Feiertage_Connector::BASE_URL.'?jahr='.$jahr.'&nur_land='.$land);
        self::$cache['c0-'.$jahr.$land] = json_decode($result,true);

        return self::$cache['c0-'.$jahr.$land];
    }

    public function getAlleFeiertage($jahr)
    {
        if(isset(self::$cache['c1-'.$jahr]))
            return self::$cache['c1-'.$jahr];

        $result = $this->_file_get_contents_t_curl(LPLib_Feiertage_Connector::BASE_URL.'?jahr='.$jahr);
        self::$cache['c1-'.$jahr] = json_decode($result,true);

        return self::$cache['c1-'.$jahr];
    }

    public function getAlleFeiertageOhneLaender($jahr)
    {
        if(isset(self::$cache['c2-'.$jahr]))
            return self::$cache['c2-'.$jahr];

        $result = $this->_file_get_contents_t_curl(LPLib_Feiertage_Connector::BASE_URL.'?jahr='.$jahr.'&nur_daten=1');
        self::$cache['c2-'.$jahr] = json_decode($result,true);

        return self::$cache['c2-'.$jahr];
    }

    public function  isFeiertagInLand($datum,$land)
    {
        $feiertage_land = $this->getFeiertageVonLand(date('Y',strtotime($datum)),$land);
        foreach($feiertage_land as $feiertagsname => $feiertagsdatum)
            if($feiertagsdatum['datum'] == date('Y-m-d',strtotime($datum)))
                return true;

        return false;
    }

    private function _file_get_contents_t_curl($url) {
        $file = @file_get_contents($url);
        if(!empty($file))
            return $file;
        else
        {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

            $data = curl_exec($ch);
            curl_close($ch);

            return $data;
        }

        throw new Exception('Verbindung zur Feiertags-API war nicht moeglich.');
    }
}