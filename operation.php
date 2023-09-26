<?php
    function verifyUser($data){
        if(! isset($data['user'])) return false;
        $user = $data['user'];
        if(! isset($data['pass'])) return false;
        $pass = $data['pass'];
        $actMD = md5($pass);
        $success = false;

        $conn = mysqli_connect("localhost", "root", "", "egzamin");
        $query = "SELECT * from loguj WHERE nazwa = ?";
        $stmt = $conn -> prepare($query);
        $stmt -> bind_param("s", $user);
        $stmt -> execute();
        $result = $stmt -> get_result();
        while($row = $result -> fetch_assoc()){
            $expectedMD = $row["haslo"];
            $allowed = $row["allowed"];
            if($allowed && $expectedMD == $actMD){
                $success = true;
            }
        }

        $stmt -> close();
        $conn -> close();

        return $success;
    }
    $op = null;
    if(isset($_POST["op"]))
        $op = $_POST["op"];
    switch($op){
        case "login":
            //Verify the user
            $verify = verifyUser($_POST);
            if($verify){
                //Set the token
                $expiry = time() + 7200;
                setcookie("user", $_POST["user"], $expiry);
                setcookie("pass", $_POST["pass"], $expiry);
                setcookie("expiry", $expiry, $expiry);
                echo "Udane logowanie jako ".$_POST["user"];
            }else{
                setcookie("user", "");
                setcookie("pass", "");
                echo "Nieprawidłowy login lub hasło";
            }
            break;
        case "logoff":
            setcookie("user", "");
            setcookie("pass", "");
        case "insert":
            //Insert
            $verify = verifyUser($_COOKIE);
            if($verify){
                $id = $_POST["id"];
                $czas = $_POST["czas"];
                $dzien = $_POST["dzien"];
                $kierunek = $_POST["kierunek"];
                $nr_rejsu = $_POST["nr_rejsu"];
                $samoloty_id = $_POST["samoloty_id"];
                $status_lotu = $_POST["status_lotu"];
                $table0 = $_POST["tab"];
                if(isset($_GET["tab"])) $table = $_GET["tab"];
                if($table0=="odloty")
                    $table = "odloty";
                else if($table0 == "przyloty")
                    $table = "przyloty";
                else $table = "ILLEGALTABLE_"+$table0;

                $conn = mysqli_connect("localhost", "root", "", "egzamin");
                if($id){
                    //Edit existing
                    $query = "UPDATE ".$table." SET czas=?, dzien=?, kierunek=?, nr_rejsu=?, samoloty_id=?, status_lotu=? WHERE id=?";
                    $stmt = $conn -> prepare($query);
                    $stmt -> bind_param("ssssisi", $czas, $dzien, $kierunek, $nr_rejsu, $samoloty_id, $status_lotu, $id);
                    $stmt -> execute();
                    $stmt -> close();
                }else{
                    //Add new
                    $query = "INSERT INTO ".$table."(czas,dzien,kierunek,nr_rejsu,samoloty_id,status_lotu) VALUES(?, ?, ?, ?, ?, ?)";
                    $stmt = $conn -> prepare($query);
                    $stmt -> bind_param("ssssis", $czas, $dzien, $kierunek, $nr_rejsu, $samoloty_id, $status_lotu);
                    $stmt -> execute();
                    $stmt -> close();
                }
                $conn -> close();
            }else{
                echo "Nie jesteś zalogowan";
            }
            break;
        case "del":
            //Delete data
            $id = $_POST["id"];
            $table0 = $_POST["tab"];
            if(isset($_GET["tab"])) $table = $_GET["tab"];
            if($table0=="odloty")
                $table = "odloty";
            else if($table0 == "przyloty")
                $table = "przyloty";
            else $table = "ILLEGALTABLE_"+$table0;

            $conn = mysqli_connect("localhost", "root", "", "egzamin");
            $query = "DELETE FROM ".$table." WHERE id = ?";
            $stmt = $conn -> prepare($query);
            $stmt -> bind_param("i", $id);
            $stmt -> execute();
            $stmt -> close();
            $conn -> close();
            break;
        default:
            echo "Nieprawidłowa operacja";
    }
?>

<br> Prekierowanie nastąpi w ciągu 10s
<script>
    setTimeout(function(){
        window.location.replace("lotnisko.php");
    }, 10000);
</script>