<?php

// nous initialisons notre ouverture de session 
    require_once('inc/init.php');


    // echo "<pre>";
    //     print_r($_POST);
    //     print_r($_FILES);
    
    // echo "</pre>";

    if ($_POST) {
        if (isset($_POST['inscription'])) {
            $result = $pdo->prepare("SELECT * FROM user WHERE pseudo= :pseudo OR mail = :email");
            $result->bindValue(':pseudo',$_POST['pseudo'],PDO::PARAM_STR);
            $result->bindValue(':email',$_POST['mail'],PDO::PARAM_STR);
            $result->execute();
        if($result->rowCount() >= 1){
            $msg .="<div class = 'alert alert-danger'>le compte existe deja</div>";
        }
            if (empty($_POST['pseudo']) || empty($_POST['password']) || empty($_POST['mail'])) {
                $msg.= "<div class='alert alert-danger' role='alert' >veuillez remplir tout les champs</div>";
            }
            
            if (empty($msg)) {
                if (!empty($_FILES['picture']['name'])) {
                    //$picture = uniqid(md5($_FILES['picture']['name']));  #ici, nous avons vu une premiere solution pour donner un nom unique a notre fichier(pour ne pas potentiellement ecraser un fichier qui aurait le meme nom)
                    //la finction uniqid me retourne un id unique et la fonction md5() quand a elle me retourne le hashage de la valeur rentrer (initialement prevu pour la securité - ex: mot de passe) 
                    
                    $picture = $_POST['pseudo'].'_'. time(). '-' . rand(1,999) . '_'.$_FILES['picture']['name']; // pour donner un nom unique au nom du chicher de l'utilisateur, concatene sont pseudo + la valeur temps au moment de l'enregistrement + un chiffre random entre 0-999 + le nom du fichier
                    
                    $picture = str_replace(' ', '-', $picture); // remplace tout les escpace creer par l'utilisateur sur le nom de la photo par des tirer: je veux etre sur de ne pas enregistrer des espace pour ce fichier
                    //et je les enregistre dans ma variable $picture
                    
                    copy($_FILES['picture']['tmp_name'], 'uploads/img/'.$picture); // la fonction copy() me permet de copier coller un fichier en prenant deux arguments : 
                    // l'endroit ou trouver mon fichier + l'endroit cibler
                }else {
                    $picture = "default.png"; 
                }
                $result = $pdo->prepare("INSERT INTO user (pseudo, password, mail, picture) VALUES (:pseudo, :password, :email, :picture)");
                
                $salt = md5('il etait une fois dans une galaxie lointaine, tres lointaine'); // salage pour rendre le mot de passe beaucoup plus compliquer a craquer
                $hashed_password = md5($_POST['password'] . $salt); // convertir mot de passe en md5 et (concatenant) rajouter le salage qui coinvertit la phrase en md5 donc double MD5
                
                $result->bindValue(":pseudo",$_POST['pseudo'], PDO::PARAM_STR);
                $result->bindValue(":password",$hashed_password, PDO::PARAM_STR);
                $result->bindValue(":email",$_POST['mail'], PDO::PARAM_STR);
                $result->bindValue(":picture",$picture, PDO::PARAM_STR);
                
                if($result->execute()) {
                    $msg.= "<div class='alert alert-success' role='alert'>vous etes bien enregistré. veuillez maintenant vous connecter </div>";
                }else {
                    $msg.= "<div class='alert alert-danger' role='alert' >un probleme est survenu veuillez reessayer</div>";
                }
            }
        }
        
        
        if (isset($_POST['conexion'])) {
            if (empty($_POST['pseudo']) || empty($_POST['password'])) {
                $msg.= "<div class='alert alert-danger' role='alert' >veuillez remplir tout les champs</div>";
            }else {
                $salt = md5('il etait une fois dans une galaxie lointaine, tres lointaine'); // salage pour rendre le mot de passe beaucoup plus compliquer a craquer
                $check_password = md5($_POST['password'] . $salt); // convertir mot de passe en md5 et (concatenant) rajouter le salage qui coinvertit la phrase en md5 donc double MD5
                
                $result = $pdo->prepare("SELECT * FROM user WHERE pseudo = :pseudo AND password = :password");
                
                $result->bindValue(":pseudo", $_POST['pseudo'], PDO::PARAM_STR);
                $result->bindValue(":password", $check_password, PDO::PARAM_STR);
                
                $result->execute();
                if ($result->rowCount() == 1) {
                    $user = $result->fetch();
                    
                    $_SESSION['pseudo'] = $user['pseudo'];
                    $_SESSION['id_user'] = $user['id_user'];
                    $_SESSION['mail'] = $user['mail'];
                    $_SESSION['picture'] = $user['picture'];
                    //o,n enregistre les informations dans notre session afin de les utiliser tres facilement 

                    header('location:tchat.php');

                    //la fonction header nous permet de rediriger l'utilisateur vers la page de notre choix si il dest connecter, nous enreggistrons dands session c'est parametre avant de lui faire acceder aau chat 
                }
                else {
                    $msg.= "<div class='alert alert-danger' role='alert' >enregistrer vous d'abord</div>";
                }
            }
        }




    }
    require_once('inc/header.php');
?>




  <main class="container mt-5">
        <div class="row justify-content-center align-items-center p-5 test">
            <div class="col-7">
                <h1>Inscription & connexion</h1>
                <form action="" method="POST" enctype="multipart/form-data">
                <!-- l'atribut enctype est tres inportant car nous souhaitons traiter l'envoie d'un fichier. sans ca, pas de données rtecuperer via la superglobal $_FILES -->
                    <?= $msg ?>
                    <small id="emailHelp" class="form-text text-muted">tes donnes ne seront pas utiliser a des fins commercial</small>

                    <div class="form-group">
                        <label for="pseudo">choissiser votre pseudo</label>
                        <input type="text" name="pseudo" class="form-control" id="pseudo" aria-describedby="pseudo" placeholder="Enter pseudo">
                    </div>
                    <div class="form-group">
                        <label for="password">choissisez votre password</label>
                        <input type="password" class="form-control" id="password" placeholder="password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="email">choissisez votre email</label>
                        <input type="email" class="form-control" id="email" placeholder="email" name="mail">
                    </div>
                    <div class="form-group">
                        <label for="file">Example file input</label>
                        <input type="file" class="form-control-file" id="file" name="picture">
                    </div>
                    <button type="submit" class="btn btn-dark" name="inscription">inscription</button><br>
                </form>  
            </div>
    </main>
        



<?php
require_once('inc/footer.php');
?>


