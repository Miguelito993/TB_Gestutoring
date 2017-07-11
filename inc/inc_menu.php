<?php
    if (isset($_SESSION['pseudo'])) {
        if($_SESSION['type'] == 'Coach'){
            echo '<ul class="nav navbar-nav">
                    <li><a href="session.php">Session</a></li>
                    <li><a id="linkAddDatas" href="#">Ajouter contenu</a></li>
                </ul>';
        }else{
            echo '<ul class="nav navbar-nav">
                    <li><a href="session.php">Session</a></li>
                </ul>';
        }
    }
?>

