<?php

declare (strict_types=1);
require_once __DIR__ . '/Settings.php'; // Settings;

function connectDb(): PDO {
    static $db = null;

    if ($db === null) {
        // Hämta settings 
        $settings = new Settings();
        // Koppla mot databasen
        $dsn = $settings->dsn;
        $dbUser = $settings->dbUser;
        $dbPassword = $settings->dbPassword;
        $db = new PDO($dsn, $dbUser, $dbPassword);
    }

    return $db;
}

function kontrolleraIndata(array $postData):array{
    $retur = [];

    //Kontrollera datum $postdata["date"]
    $datum = DateTimeImmutable::createFromFormat("Y-m-d", $postData["date"] ?? "");
    if(!$datum){
        $retur[] = "Ogiltigt angivet datum";
    }
    if($datum && $datum -> format("Y-m-d") !== $postData["date"]){
        $retur[] = "Felaktig formaterat datum";
    }
    if($datum && $datum -> format("Y-m-d") > date("Y-m-d")){
        $retur[] = "Datum får inte vara framåt i tiden";
    }

    //Kontrollera tid $postdata["time"]
    $tid = DateTimeImmutable::createFromFormat("H:i", $postData["time"] ?? "");
    if(!$tid) {
        $retur[] = "Ogiltigt angiven tid";
    }
    if($tid && $tid -> format("H:i") !== $postData["time"]){
        $retur[] = "Felaktigt angiven tid";
    }
    if($tid && $tid -> format("H:i") > "08:00") {
        $retur[] = "Du får inte rapportera mer än 8 timmar per aktivitet år gången";
    }

    //Kontrollera aktivitetsId $postdata["activityId"]
    $aktivitet = hamtaEnskildAktivitet($postData["activityId"] ?? "");
    if($aktivitet -> getStatus() === 400){
        $retur[] = "Angiven aktivitets id saknas";
    }

    return $retur;
}