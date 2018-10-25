<?php 

require_once('inc/init.php');



if (empty($_SESSION['pseudo'])) {
    header('location:index.php');
    die();
}



if ($_POST && !empty($_POST['message'])) {
    $result = $pdo->prepare("INSERT INTO comment (id_user, content, datetime) VALUES ('$_SESSION[id_user]', :message, NOW())");
    
    $result->bindValue(':message', $_POST['message'], PDO::PARAM_STR);
    if ($result->execute()) {
        header('location:tchat.php');
    }else {
        $msg .="<div class='alert alert-danger'>votre message ne c'est pas enregistrer veuiller reessayer</div>";
        
    }
}//else {
//     $msg .="<div class='alert alert-danger'>rentreer un message valide</div>";
// }

//requete de selection pour afficher tout les messages 

$req = "SELECT u.*, c.* 
FROM user u, comment c 
WHERE u.id_user = c.id_user 
ORDER BY c.datetime ASC";

$result = $pdo->query($req);
$messages = $result->fetchAll();

// echo "<pre>";
//     print_r($messages);
// echo "</pre>";

# deconnexion si jamais 
if (isset($_GET['a']) && $_GET['a'] == 'deconnect') {
    session_destroy();
    header('location:index.php');
}


require_once('inc/header.php');
?>




<div class="container mt-5">
<h1>Tchater avec vos potes</h1>


<?php foreach ($messages as $message) : ?>
    <?php extract ($message) ?>
    <?php if($id_user == $_SESSION['id_user']) : ?>
    <div class="card ml-5">
  <div class="card-header">
    <img style="width: 8rem;" src="uploads/img/<?= $picture ?>" class="img-thumbnail float-right" alt="">  </div>
  <div class="card-body">
    <h5 class="card-title alert alert-primary"><?= htmlspecialchars($pseudo) ?></h5>
    <p class="card-text"><?= htmlspecialchars($content) ?></p>
    <p class="card-subtitle mb-2 text-muted"><?= $datetime?></p>
  </div>
</div>

<?php else : ?>
    <div class="card mr-5">
  <div class="card-header">
    <img style="width: 5rem;" src="uploads/img/<?= $picture ?>" class="img-thumbnail float-left" alt="">  </div>
  <div class="card-body">
    <h5 class="card-title alert alert-success"><?= htmlspecialchars($pseudo) ?></h5>
    <p class="card-text"><?= htmlspecialchars($content) ?></p>
    <p class="card-subtitle mb-2 text-muted"><?= $datetime?></p>
  </div>
</div>
<?php endif; ?>
<?php endforeach; ?>
    <h2>Envoyer un nouveau message: </h2>
    <form method="POST">
        <div class="form-group">
          <textarea type="text" name="message" id="message" class="form-control" rows="3" placeholder="partager vos idées..."></textarea>
        </div>
        <input type="submit" value="envoyer le message" class="btn btn-info">
    </form>
<br>
<br>
<br>
<?=$msg?>
<a href="?a=deconnect" class="btn btn-warning">deconnexion</a>
</div>
<?php

require_once('inc/footer.php');

?>


