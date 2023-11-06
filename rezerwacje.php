<?php 

$data = date('Y-m-d', strtotime($_POST['data2']));

$db = new mysqli("localhost", "root", "", "balety");
$q = "SELECT numer_sali, data, godzina_Rozpoczecia, godzina_Zakonczenia FROM `sale` WHERE data = '$data'";
$result = $db->query($q);
if($result->num_rows === 0) {
    echo("W tym dniu nie ma żadnych rezerwacji");
}
while($row = $result->fetch_assoc()) {

    echo ("W tym dniu odbywają się na sali nr" . $row['numer_sali']. " w godzinach od: " . $row['godzina_Rozpoczecia'] . " do: " . $row['godzina_Zakonczenia']);

}




?>