<!-- Fixed navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
      <a class="navbar-brand" href="./index.php">GesTutoring</a>
    </div>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <?php
        if(isset($_SESSION['user'])){
            include './inc/inc_menu.php';
        }
        ?>
      <ul class="nav navbar-nav navbar-right">
        <?php
            if(!isset($_SESSION['user'])){
                echo '<li><a href="./connexion.php">Se connecter</a></li><li><a href="./inscription.php">S\'inscrire</a></li>';
            }else{
                echo '<li><a href="./logout.php">Bienvenue '.$_SESSION['user'].' [Déconnexion]</a></li>';
            }
        ?>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</nav>
