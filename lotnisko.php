<?php
    if(isset($_COOKIE["lastvisit"])){
        //Recently visited
        $cookies = '<p class="cookie-recent">Witaj ponownie na stronie lotniska</p>';
    }else{
        //Visited earlier
        $cookies = '<p class="cookie-return">Dzień dobry. Strona lotniska używa ciasteczek</p>';
    }
    setcookie("lastvisit", "abc", time() + 7200);
?>
<html>
    <head>
        <meta charset="utf8">
        <title>Port Lotniczy</title>
        <link rel="stylesheet" href="styl5.css"/>
    </head>
    <body>
        <section id="banner">
            <section id="left">
                <img src="zad5.png" alt="logo lotnisko"/>
            </section>
            <section id="center">
                <h1><?php
                    $table = null;
                    if(isset($_GET["tab"])) $table = $_GET["tab"];
                    $from = "00:00";
                    if(isset($_GET["from"])) $from = $_GET["from"];
                    $to= "23:59";
                    if(isset($_GET["to"])) $to = $_GET["to"];

                    if($table=="odloty"){
                        $table = "odloty";
                        $other = "przyloty";
                    }else{
                        $table = "przyloty";
                        $other = "odloty";
                    }
                    $title = ucfirst($table);
                    $titleOther = ucfirst($other);

                    echo $title;
                ?></h1>
            </section>
            <section id="selform">
                <!-- Formularz pobierania danych -->
                <form id="selform" name="custom" action="lotnisko.php">
                         Od włącznie: <input name="from" type="time" value="00:00">
                    <br> Do włącznie: <input name="to" type="time" value="23:59">
                    <br> Tabela: <select name="tab">
                        <option>przyloty</option>
                        <option>odloty</option>
                    </select>
                    <input type="submit" value="Zobacz"/>
                </form>
            </section>
            <section id="right">
                <!-- Czy użytkownik jest zalogowany? -->
                <?php 
                    if(isset($_COOKIE["user"])){
                        $user = $_COOKIE["user"];
                        $expiry = $_COOKIE["expiry"];
                        $expiry0 = date("Y-m-d H:i:s", intval($expiry));
                        echo '<h3 class="logout"> Cześć ', $user;
                        echo '<form name="logout" action="operation.php" method="POST" class="logout">';
                        echo '<input type="hidden" name="op" value="logoff"/>';
                        echo '<input type="submit" value="Wyloguj się"/></form>';
                        echo "Sesja wygasa: ", $expiry0, "</h3>";
                    }else{
                        echo '<h3 class="logout">Zaloguj się</h3>';
                    }
                ?>
                <!-- Formularz logowania, Login:admin, hasło:password -->
                <form name="logon" method="POST" action="operation.php" class="logout">
                         <input name="op" type="hidden" value="login"/>
                         <input name="user" type="text" value="Nazwa użytkownika"/>
                    <br> <input name="pass" type="password" value="Hasło"/>
                    <br> <input type="submit" value="Zaloguj się"/>
                </form>
                <br> <a href="kwerendy.txt" target="__blank">Pobierz...</a>
            </section>
        </section>
        <section id="main">
            <table>
                <tr>
                    <th>ID <?php
                        global $decl;
                        $decl = substr($table, 0, strlen($table)-1)."u";
                        echo $decl;
                    ?></th>
                    <th>czas</th>
                    <th>data</th>
                    <th>kierunek</th>
                    <th>numer rejsu</th>
                    <th>numer samolotu</th>
                    <th>status</th>
                </tr>
                <?php
                    $conn = mysqli_connect("localhost", "root", null, "egzamin");
                    $query = "SELECT czas, kierunek, nr_rejsu, status_lotu, id, samoloty_id, dzien FROM ".$table." WHERE czas >= ? AND czas <= ? ORDER BY czas ASC";
                    $stmt = $conn -> prepare($query);
                    $stmt ->bind_param("ss", $from, $to);
                    $stmt -> execute();
                    $result = $stmt -> get_result();
                    while($row = mysqli_fetch_row($result)){
                        $czas = $row[0];
                        $kierunak = $row[1];
                        $nr = $row[2];
                        $status = $row[3];
                        $id = $row[4];
                        $samolot = $row[5];
                        $day = $row[6];
                        echo "<tr><td>", $id, "</td><td>", $czas, "</td><td>", $day, "</td><td>", $kierunak, "</td><td>", $nr, "</td><td>", $samolot, "</td><td>", $status, "</td></tr>";
                    }
                    mysqli_close($conn);
                ?>
            </table>
            <!-- Formularz dodawania/edycji -->
            <form name="edit" method="POST" action="operation.php">
                <?php echo '<input name="tab" type="hidden" value="'.$table.'"/>'?>
                <input name="op" type="hidden" value="insert"/>
                Wpisz ID <?php echo $decl; ?> do edycji, lub 0 do dodania nowego przylotu
                <input type="submit" value="Prześlij"/>
                <table>
                    <tr>
                        <th>ID lub wstaw</th>
                        <th>Czas</th>
                        <th>Data</th>
                        <th>kierunek</th>
                        <th>Numer rejsu</th>
                        <th>Numer samolotu</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td><input type="number" name="id" value=0/></td>
                        <td><input type="time" name="czas" value=0/></td>
                        <td><input type="date" name="dzien" value=0/></td>
                        <td><input type="text" name="kierunek" value=0/></td>
                        <td><input type="text" name="nr_rejsu" value=0/></td>
                        <td><input type="number" name="samoloty_id" value=0/></td>
                        <td><input type="text" name="status_lotu" value="0"/></td>
                    </tr>
                </table>
            </form>
            <!-- Formularz usuwania -->
            <form name="del" method="POST" action="operation.php">
            <?php echo '<input name="tab" type="hidden" value="'.$table.'"/>'?>
                <input name="op" type="hidden" value="del"/>
                ID <?php echo $decl; ?> do usunięcia: <input type="number" name="id" value=0/>
                <input type="submit" value="Usuń"/>
            </form>
        </section>
        <section id="foot">
            <section id="foot1">
                <?php echo $cookies; ?>
            </section>
            <section id="foot2">
                Autor strony: Oskar Balcerzak
            </section>
        </section>
    </body>
</html>