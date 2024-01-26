<?php

declare (strict_types=1);
require_once '../src/activities.php';

/**
 * Funktion för att testa alla aktiviteter
 * @return string html-sträng med resultatet av alla tester
 */
function allaActivityTester(): string {
    // Kom ihåg att lägga till alla funktioner i filen!
    $retur = "";
    $retur .= test_HamtaAllaAktiviteter();
    $retur .= test_HamtaEnAktivitet();
    $retur .= test_SparaNyAktivitet();
    $retur .= test_UppdateraAktivitet();
    $retur .= test_RaderaAktivitet();

    return $retur;
}

/**
 * Tester för funktionen hämta alla aktiviteter
 * @return string html-sträng med alla resultat för testerna 
 */
function test_HamtaAllaAktiviteter(): string {
    $retur = "<h2>test_HamtaAllaAktiviteter</h2>";
    try {
        $svar = hamtaAllaAktiviteter();
        if($svar -> getStatus() === 200){
            $retur .= "<p class='ok'>Hämta alla aktiviteter lyckades " . count($svar -> getContent() -> activities)
            . " poster returnerades</p>";
        }else{

            $retur .= "<p class='error'>Hämta alla aktiviteter misslyckades<br>"
            . $svar -> getStatus() .  " returnerades</p>";
        }
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}

/**
 * Tester för funktionen hämta enskild aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_HamtaEnAktivitet(): string {
    $retur = "<h2>test_HamtaEnAktivitet</h2>";

    try {
        //Hämta post -1
        $svar = hamtaEnskildAktivitet("-1");
        if($svar -> getStatus() === 400){
            $retur .= "<p class='ok'>Hämta post med id -1 misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Hämta post med id -1 returnerade " . $svar -> getStatus()
            . " returnerades istället som förväntat 400</p>";
        }

        //Misslyckas hämta post id=0
        $svar = hamtaEnskildAktivitet("0");
        if($svar -> getStatus() === 400){
            $retur .= "<p class='ok'>Hämta post med id 0 misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Hämta post med id 0 returnerade " . $svar -> getStatus()
            . " returnerades istället som förväntat 400</p>";
        }

        //Misslyckas hämta post id=3.14
        $svar = hamtaEnskildAktivitet("3.14");
        if($svar -> getStatus() === 400){
            $retur .= "<p class='ok'>Hämta post med id 3.14 misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Hämta post med id 3.14 returnerade " . $svar -> getStatus()
            . " returnerades istället som förväntat 400</p>";
        }

        //Koplla databas
        $db = connectDb();

        //Skapa transaktion
        $db -> beginTransaction();

        //Skapa ny post för att vara säker att posten finns
        $nyAktivitet = "Aktivitet" . time();
        $giltigtId = sparaNyAktivitet($nyAktivitet);
        if($giltigtId -> getStatus() === 200){
            $giltigtId = $giltigtId -> getContent() -> id;
        } else {
            throw new Exception("Kunde inte skapa ny post för kontroll");
        }

        //Lyckas hämta skapad post
        $svar = hamtaEnskildAktivitet("$giltigtId");
        if($svar -> getStatus() === 200) {
            $retur .= "<p class='ok'>Hämta en aktivitet med id ($giltigtId) gick bra</p>";
        } else {
            $retur .= "<p class='error'>Hämta en aktivitet misslyckades, status " . $svar -> getStatus()
            . " returnerades istället som förväntat 400</p>";
        }

        //Misslyckas med att hämta post med id +1

        $giltigtId++;
        $svar = hamtaEnskildAktivitet("$giltigtId");
        if($svar -> getStatus() === 400) {
            $retur .= "<p class='ok'>Hämta en aktivitet med ett id som saknas misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Hämta en aktivitet med ett id som saknas lyckades, ovförväntat, status " . $svar -> getStatus()
            . " returnerades istället som förväntat 200</p>";
        }
        
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    } finally{
        //Återställ databasen
        $db -> rollBack();
    }

    return $retur;
}

/**
 * Tester för funktionen spara aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_SparaNyAktivitet(): string {
    $retur = "<h2>test_SparaNyAktivitet</h2>";

    $nyAktivitet = "Aktivitet" . time();
 
    try {
        //koppla databas
        $db = connectDb();

        //Starta transaction
        $db -> beginTransaction();

        //Spara tom aktivitet - Misslyckat
        $svar = sparaNyAktivitet("");
        if($svar -> getStatus() === 400) {
            $retur .= "<p class='ok'>Spara tom aktivitet misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Spara tom aktivitet lyckade, oförväntat, status " . $svar -> getStatus()
            . " returnerades istället som förväntat 400</p>";
        }

        //Spara ny Aktivitet - Lyckat
        $svar = sparaNyAktivitet($nyAktivitet);
        if($svar -> getStatus() === 200) {
            $retur .= "<p class='ok'>Spara aktivitet lyckades</p>";
        } else {
            $retur .= "<p class='error'>Spara aktivitet misslyckades, status " . $svar -> getStatus()
            . " returnerades istället som förväntat 200</p>";
        }
        
        //Spara ny Aktivitet - Misslyckat
        $svar = sparaNyAktivitet($nyAktivitet);
        if($svar -> getStatus() === 400) {
            $retur .= "<p class='ok'>Spara duplicerad aktivitet misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Spara duplicerad aktivitet lyckades, oförväntat, status " . $svar -> getStatus()
            . " returnerades istället som förväntat 400</p>";
        }
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    } finally {
        //Återställ databasen
        if($db) {
            $db -> rollBack();
        }
    }

    return $retur;
}

/**
 * Tester för uppdatera aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_UppdateraAktivitet(): string {
    $retur = "<h2>test_UppdateraAktivitet</h2>";

    try {
        //Koppla databasen
        $db = connectDb();
        //Starta transaktion
        $db -> beginTransaction();

        //Misslyckas med att uppdatera id = -1
        $svar = uppdateraAktivitet("-1", "Aktivitet");
        if($svar -> getStatus() === 400){
            $retur .= "<p class='ok'>Uppdatera aktivitet med id = -1 misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Uppdaterad aktivitet med id = -1 lyckades, status " .  $svar -> getStatus() . " istället för förväntad 400</p>";
        }

        //Misslyckas med att uppdatera id = 0
        $svar = uppdateraAktivitet("0", "Aktivitet");
        if($svar -> getStatus() === 400){
            $retur .= "<p class='ok'>Uppdatera aktivitet med id = 0 misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Uppdatera aktivitet med id = 0 lyckades, status " .  $svar -> getStatus() . " istället för förväntad 400</p>";
        }

        //Misslyckas med att uppdatera id = 3.14
        $svar = uppdateraAktivitet("3.14", "Aktivitet");
        if($svar -> getStatus() === 400){
            $retur .= "<p class='ok'>Uppdatera aktivitet med id = 3.14 misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Uppdatera aktivitet med id = 3.14 lyckades, status " .  $svar -> getStatus() . " istället för förväntad 400</p>";
        }

        //Misslyckas med att uppdatera aktivitet = ""
        $nyAktivitet = "Aktivitet" . time();
        $giltigtId = sparaNyAktivitet($nyAktivitet);
        if($giltigtId -> getStatus() === 200){
            $giltigtId = $giltigtId -> getContent() -> id;
        } else {
            throw new Exception("Kunde inte skapa ny post för kontroll");
        }

        $svar = uppdateraAktivitet("$giltigtId", "");
        if($svar -> getStatus() === 400){
            $retur .= "<p class='ok'>Uppdatera aktivitet med aktivitet = '' misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Uppdatera aktivitet med aktivitet = '' lyckades, status " .  $svar -> getStatus() . " istället för förväntad 400</p>";
        }

        //Lyckas med att uppdatera aktivitet
        $svar = uppdateraAktivitet("$giltigtId", "$nyAktivitet");
        if($svar -> getStatus() === 200){
            $retur .= "<p class='ok'>Uppdatera aktivitet lyckades</p>";
        } else {
            $retur .= "<p class='error'>Uppdatera aktivitet misslyckades, status " .  $svar -> getStatus() . " istället för förväntad 200</p>";
        }

        //Uppdatera med samma information misslyckas
        $svar = uppdateraAktivitet("$giltigtId", "$nyAktivitet");
        if($svar -> getStatus() === 200 && $svar -> getContent() -> result === false){
            $retur .= "<p class='ok'>Uppdatera aktivitet med samma innehåll misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Uppdaterad aktivitet med samma innehåll lyckades, status " .  $svar -> getStatus() . " med informationen: <br>" . print_r($svar -> getContent(), true) . "</p>";
        }
    
        //Misslyckas med att updatera aktivitet med en aktivitet som redan finns
        $nyAktivitet2 = "Aktivitet2" . time();
        $giltigtId2 = sparaNyAktivitet($nyAktivitet2);
        if($giltigtId2 -> getStatus() === 200){
            $giltigtId2 = $giltigtId2 -> getContent() -> id;
        } else {
            throw new Exception("Kunde inte skapa ny post för kontroll");
        }

        $svar = uppdateraAktivitet("$giltigtId2", "$nyAktivitet");
        if($svar -> getStatus() === 400){
            $retur .= "<p class='ok'>Uppdatera aktivitet som redan finns misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Uppdatera aktivitet som redan finns lyckades, status " .  $svar -> getStatus() . " istället för förväntad 400</p>";
        }

        //Misslyckas med att uppdatera aktivitet som inte finns
        $giltigtId2 ++;
        $svar = uppdateraAktivitet("$giltigtId2", "$nyAktivitet");
        if($svar -> getStatus() === 200 && $svar -> getContent() -> result === false){
            $retur .= "<p class='ok'>Uppdatera aktivitet som inte finns misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Uppdatera aktivitet som inte finns lyckades, status:" .  $svar -> getStatus() . " med informationen: <br>" . print_r($svar -> getContent(), true) . "</p>";
        }

    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    } finally {
        //Återställ databasen
        if($db) {
            $db -> rollBack();
        }
    }

    return $retur;
}

/**
 * Tester för funktionen radera aktivitet
 * @return string html-sträng med alla resultat för testerna 
 */
function test_RaderaAktivitet(): string {
    $retur = "<h2>test_RaderaAktivitet</h2>";
    try {
        //Testa felaktig id
        $svar = raderaAktivitet("-1");
        if($svar -> getStatus() === 400){
            $retur .= "<p class='ok'>Radera aktivitet med felaktigt id (-1) misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Radera aktivitet med felaktigt id (-1) lyckades, status " .  $svar -> getStatus() . " istället för förväntad 400</p>";
        }

        $svar = raderaAktivitet("0");
        if($svar -> getStatus() === 400){
            $retur .= "<p class='ok'>Radera aktivitet med felaktigt id (0) misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Radera aktivitet med felaktigt id (0) lyckades, status " .  $svar -> getStatus() . " istället för förväntad 400</p>";
        }

        $svar = raderaAktivitet("3.14");
        if($svar -> getStatus() === 400){
            $retur .= "<p class='ok'>Radera aktivitet med felaktigt id (3.14) misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Radera aktivitet med felaktigt id (3.14) lyckades, status " .  $svar -> getStatus() . " istället för förväntad 400</p>";
        }


        //Testa radera befintligt
        $db = connectDb();
        $db -> beginTransaction();

        $nyAktivitet = "Aktivitet" . time();
        $giltigtId = sparaNyAktivitet($nyAktivitet);
        if($giltigtId -> getStatus() === 200){
            $giltigtId = $giltigtId -> getContent() -> id;
        } else {
            throw new Exception("Kunde inte skapa ny post för kontroll");
        }

        $svar = raderaAktivitet($giltigtId);
        if($svar -> getStatus() === 200 && $svar -> getContent() -> result === true){
            $retur .= "<p class='ok'>Radera aktivitet lyckades</p>";
        } else {
            $retur .= "<p class='error'>Radera aktivitet misslyckades.<br>" .  $svar -> getStatus() . " och ". var_export($svar -> getContent() -> result, true) . " istället för förväntad 200 och false</p>";
        }
        $db -> rollBack();

        //Testa radera som inte finns

        $svar = raderaAktivitet($giltigtId);
        if($svar -> getStatus() === 200 && $svar -> getContent() -> result === false){
            $retur .= "<p class='ok'>Radera aktivitet som inte finns misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Radera aktivitet lyckades.<br>" .  $svar -> getStatus() . " och ". var_export($svar -> getContent() -> result, true) . " istället för förväntad 200 och false</p>";
        }

    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }
    
    return $retur;
}
