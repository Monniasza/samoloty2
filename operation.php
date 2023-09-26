<?php
    function verifyTable($table){
        if($table=="odloty")
            return "odloty";
        else if($table == "przyloty")
            return "przyloty";
        else
            return null;
    }
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

    function run(){
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
                    return 10000;
                }else{
                    setcookie("user", "");
                    setcookie("pass", "");
                    echo "Nieprawidłowy login lub hasło";
                    return 10000;
                }
            case "logoff":
                setcookie("user", "");
                setcookie("pass", "");
                return 3000;
            case "insert":
                //Insert
                $verify = verifyUser($_COOKIE);
                if(!$verify){
                    echo "Nie jesteś zalogowan";
                    return 10000;
                }
                
                $table0 = $_POST["tab"];
                $table = verifyTable($table0);
                if($table == null){
                    echo "Niepoprawna tabela ", $table0;
                    return 10000;
                }

                $id = $_POST["id"];
                $czas = $_POST["czas"];
                $dzien = $_POST["dzien"];
                $kierunek = $_POST["kierunek"];
                $nr_rejsu = $_POST["nr_rejsu"];
                $samoloty_id = $_POST["samoloty_id"];
                $status_lotu = $_POST["status_lotu"];

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
                return 0;
            case "del":
                //Delete data
                $verify = verifyUser($_COOKIE);
                if(!$verify){
                    echo "Nie jesteś zalogowan";
                    return 10000;
                }
                
                $table0 = $_POST["tab"];
                $table = verifyTable($table0);
                if($table == null){
                    echo "Niepoprawna tabela ", $table0;
                    return 10000;
                }

                $id = $_POST["id"];
                $conn = mysqli_connect("localhost", "root", "", "egzamin");
                $query = "DELETE FROM ".$table." WHERE id = ?";
                $stmt = $conn -> prepare($query);
                $stmt -> bind_param("i", $id);
                $stmt -> execute();
                $stmt -> close();
                $conn -> close();
                return 0;
            default:
                echo "Nieprawidłowa operacja ", $op;
        }
    }

    $delay = run();
?>

<?php
    if($delay){
        echo "<br> Prekierowanie nastąpi w ciągu ",($delay/1000),"s <script defer>setTimeout(function(){window.location.replace('lotnisko.php');}, ", $delay, ");</script>";
    }else{
        echo "<script defer>window.location.replace('lotnisko.php');</script>";
    }
?>