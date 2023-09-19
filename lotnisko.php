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
                <h3>przydatne linki</h3>
                <br> <a href="kwerendy.txt" target="__blank">Pobierz...</a>
            </section>
        </section>
        <section id="main">
            <table>
                <tr>
                    <th>czas</th>
                    <th>kierunek</th>
                    <th>numer rejsu</th>
                    <th>status</th>
                </tr>
                <?php
                    $conn = mysqli_connect("localhost", "root", null, "egzamin");
                    $query = "SELECT czas, kierunek, nr_rejsu, status_lotu FROM przyloty ORDER BY czas ASC";
                    $result = mysqli_query($conn, $query);
                    while($row = mysqli_fetch_row($result)){
                        $czas = $row[0];
                        $kierunak = $row[1];
                        $nr = $row[2];
                        $status = $row[3];
                        echo "<tr><td>", $czas, "</td><td>", $kierunak, "</td><td>", $nr, "</td><td>", $status, "</td></tr>";
                    }
                    mysqli_close($conn);
                ?>
            </table>
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