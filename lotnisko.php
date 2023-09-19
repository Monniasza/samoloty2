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
                <h1>Przyloty</h1>
            </section>
            <section id="right">
                <!-- Czy użytkownik jest zalogowany? -->
                <h3><?php 
                    if(isset($_COOKIE["user"])){
                        $user = $_COOKIE["user"];
                        echo "Cześć ", $user;
                    }else{
                        echo "Zaloguj się";
                    }
                ?></h3>
                <!-- Formularz logowania, Login:admin, hasło:password -->
                <form name="logon" method="POST" action="operation.php">
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
                    <th>ID przylotu</th>
                    <th>czas</th>
                    <th>data</th>
                    <th>kierunek</th>
                    <th>numer rejsu</th>
                    <th>numer samolotu</th>
                    <th>status</th>
                </tr>
                <?php
                    $conn = mysqli_connect("localhost", "root", null, "egzamin");
                    $query = "SELECT czas, kierunek, nr_rejsu, status_lotu, id, samoloty_id, dzien FROM przyloty ORDER BY czas ASC";
                    $result = mysqli_query($conn, $query);
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
                <input name="op" type="hidden" value="insert"/>
                Wpisz ID przylotu do edycji, lub 0 do dodania nowego przylotu
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
                        <td><input type="text" name="status_lotu" value=0/></td>
                    </tr>
                </table>
            </form>
        </section>
        <section id="foot">
            <section id="foot1">
                <?php
                    if(isset($_COOKIE["lastvisit"])){
                        //Recently visited
                        echo '<p class="cookie-recent">Witaj ponownie na stronie lotniska</p>';
                    }else{
                        //Visited earlier
                        echo '<p class="cookie-return">Dzień dobry. Strona lotniska używa ciasteczek</p>';
                    }
                    setcookie("lastvisit", "abc", time() + 7200);
                ?>
            </section>
            <section id="foot2">
                Autor strony: Oskar Balcerzak
            </section>
        </section>
    </body>
</html>