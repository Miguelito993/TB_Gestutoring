<div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="./index.php">
                <span><img alt="Logo" height="35" width="35" src="./assets/img/logo_GesTutoring.ico"></span>GesTutoring
            </a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <?php
            if (isset($_SESSION['pseudo'])) {
                if(($_SESSION['type'] == 'Coach') || ($_SESSION['type'] == 'Student')){
                    include './inc/inc_menu.php';
                }else{
                    include './inc/inc_menu_validation.php';
                }
            }
            ?>
            <ul class="nav navbar-nav navbar-right">
                <?php
                if (!isset($_SESSION['pseudo'])) {
                    echo '<li><form id="connexForm" class="navbar-form">
                        <div class="form-group">
                            <input type="text" id="inputLogPseudo" class="form-control" placeholder="Pseudo" required> 
                        </div>
                        <div class="form-group">
                            <input type="password" id="inputLogPassword" class="form-control" placeholder="Mot de passe" required>
                        </div>
                        <button type="submit" id="submitConnexion" class="btn btn-default">Connexion</button>
                      </form></li>
                      <li><a data-toggle="modal" data-target="#myInscription">S\'inscrire</a></li>';
                } else {
                    echo '<li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Bienvenue ' . $_SESSION['pseudo'] . '<span class="caret"></span></a>
                            <ul class="dropdown-menu">                                
                                <li><a href="./profil.php" class="glyphicon glyphicon-user" aria-hidden="true"> Profil</a></li>
                                <li><a href="./logout.php" class="glyphicon glyphicon-off" aria-hidden="true"> Déconnexion</a></li>                      
                            </ul>
                          </li>';
                }
                ?>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
