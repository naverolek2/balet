<?php
// nie pokazuje warningów
error_reporting(E_ALL ^ E_WARNING); 
clearstatcache();

if($_POST['do'] && $_POST['od'] && $_POST['data'] && $_POST['numerSali'] ) {
    
    $data = date('Y-m-d', strtotime($_POST['data']));
    $obecnaData = date('Y-m-d');
    $numerSali = $_POST['numerSali'];
    //sprawdza czy data jest dobrze podana tzw. czy jest o co najmniej 1 dzień większa od obecnej

    if($data <= $obecnaData) {
        die("Podałeś datę w przeszłości lub dzień dzisiejszy. Rezerwacje przyjmujemy tylko na dzień następny");
    }
    

    $odGodzina = $_POST['od'];
    $db = new mysqli("localhost", "root", "", "balety");
    $doGodzina = $_POST['do'];
    $godzinaRozpoczecia = new DateTime("$data $odGodzina");
    $godzinaZakonczenia = new DateTime("$data $doGodzina");

//sprawdza czy godziny się zgadzają
    if($godzinaRozpoczecia >= $godzinaZakonczenia) {
        die("Godzina zakończenia wypożyczania nie może być mniejsza lub równa godzinie rozpoczęcia");
    }
    $ileCzasu = $godzinaRozpoczecia->diff($godzinaZakonczenia);
    if($ileCzasu->h > 10) {
        die("Rezerwacja może być na maksymalnie 10 godzin");
    }

    // bez tego Y-m-d nie działo. PHP. 
    $GodzinaRozpoczecia = $godzinaRozpoczecia->format('Y-m-d H:i:s');
    $GodzinaZakonczenia = $godzinaZakonczenia->format('Y-m-d H:i:s');
    $q = "SELECT numer_sali, data, czas_trwania FROM `rezerwacje` WHERE data = '$data' AND numer_Sali = '$numerSali' limit 1";
    $result = $db->query($q);
    
        
    if($result->num_rows === 0) {
        $q = "INSERT INTO `sale`(`numer_Sali`, `godzina_Rozpoczecia`, `godzina_Zakonczenia`, `data`, `czas_Trwania`) VALUES ('$numerSali','$GodzinaRozpoczecia','$GodzinaZakonczenia','$data', '$ileCzasu->h:00:00')";
        $db->query($q);
        $q = "INSERT INTO `rezerwacje`(`numer_Sali`, `data`,  `czas_trwania`) VALUES ($numerSali ,'$data' ,'$ileCzasu->h:00:00')";
        $db->query($q);

    } else {
        while($row = $result->fetch_assoc()) {
            $iloscCzasu = $row['czas_trwania'] + $ileCzasu->h;
                
                if($row['data'] == $data) {
                    if($iloscCzasu > 10) {
                        $ilegodzinwynaj = $row['czas_trwania'] + $ileCzasu->h - 10;
                        die("Sala ma limit 10 godzin wypożyczenia na dobę. Przekroczyłeś/aś ją o $ilegodzinwynaj godziny");
                    } 
                    else {
                        $qu = "SELECT godzina_Rozpoczecia, godzina_Zakonczenia, numer_Sali FROM `sale` WHERE data = '$data' AND numer_Sali = '$numerSali' limit 1";
                        
                        $results = $db->query($qu);
                        while($row = $results->fetch_assoc()) {
                            $gRozp = $row['godzina_Rozpoczecia']; 
                            $gZako = $row['godzina_Zakonczenia'];
                            if($gRozp >= $odGodzina && $gRozp <= $doGodzina) {
                                die("Te godziny są już zarezerwowane");
                            }
                            if($gZako >= $odGodzina && $gZako <= $doGodzina ) {
                                die("Te godziny są już zarezerwowane");
                            }
                        }


                        $q = "UPDATE `rezerwacje` SET `czas_trwania` = ADDTIME(czas_trwania, '$ileCzasu->h:00:00') WHERE data = '$data' AND numer_Sali = '$numerSali'";
                        $db->query($q);
                        $godzRoz = $godzinaRozpoczecia->format('H:i');
                        $godzZak = $godzinaZakonczenia->format('H:i');
                        echo("Dodano rezerwacje na $ileCzasu->h godzin w dniu $data od godziny $godzRoz do godziny $god zZak");
                    }
                    
                }
    }


      }
    
    }
        
                 
            
        
        
    
   
    

else {
    die("Podaj wszystkie dane");
}
?>