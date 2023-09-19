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

    $op = $_POST["op"];
    echo $op;
    //$user = $_COOKIE["user"];
    //$pass = $_COOKIE["pass"];
    switch($op){
        case "login":
            //Verify the user
            $verify = verifyUser($_POST);
            if($verify){
                //Set the token
                setcookie("user", $_POST["user"]);
                setcookie("pass", $_POST["pass"]);
            }else{
                setcookie("user", "");
                setcookie("pass", "");
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

                $conn = mysqli_connect("localhost", "root", "", "egzamin");
                if($id){
                    //Edit existing
                    $query = "UPDATE odloty SET czas=?, dzien=?, kierunek=?, nr_rejsu=?, samoloty_id=?, status_lotu=? WHERE id=?";
                    $stmt = $conn -> prepare($query);
                    $stmt -> bind_param("ssssnsn", $czas, $dzien, $kierunek, $nr_rejsu, $samoloty_id, $status_lotu, $id);
                    $stmt -> execute();
                }else{
                    //Add new
                }
                $conn -> close();
            }else{
                echo "Nie jesteś zalogowan";
            }
            break;
    }
?>

Prekierowanie nastąpi w ciągu 10s
<script>
    setTimeout(function(){
        window.location.replace("samoloty.php");
    }, 10000);
</script>