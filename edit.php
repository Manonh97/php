<?php
// On démarre une session
session_start();
if($_POST){
    if(isset($_POST['id_plat']) && !empty($_POST['id_plat'])
    && isset($_POST['nom_plat']) && !empty($_POST['nom_plat'])
    && isset($_POST['prix_plat']) && !empty($_POST['prix_plat'])
    && isset($_POST['type_plat']) && !empty($_POST['type_plat'])
    && isset($_POST['desc_plat']) && !empty($_POST['desc_plat'])
    
){
    
    //ajouter l'image
    $currentDirectory = getcwd();
    $uploadDirectory = "/images/";
     //$fileExtensionsAllowed = ['jpeg','jpg','png']; // These will be the only file extensions allowed 
    
     $fileName = $_FILES['image']['name'];
     if ($fileName == '') { $fileName = $_POST['recup_nom_image'];}
     //$fileSize = $_FILES['image']['size'];
     $fileTmpName  = $_FILES['image']['tmp_name'];
     //$fileType = $_FILES['image']['type'];
     //$fileExtension = strtolower(end(explode('.',$fileName)));
 
     $uploadPath = $currentDirectory . $uploadDirectory . basename($fileName); 
 
     $didUpload = move_uploaded_file($fileTmpName, $uploadPath);
 
 


        // On inclut la connexion à la base
        require_once('connect.php');

        // On nettoie les données envoyées
        $id_plat = strip_tags($_POST['id_plat']);
        $nom_plat = strip_tags($_POST['nom_plat']);
        $prix_plat = strip_tags($_POST['prix_plat']);
        $desc_plat = strip_tags($_POST['desc_plat']);
        $type_plat = strip_tags($_POST['type_plat']);
        //$imageprod = $_POST['image'];
        $imageprod = $fileName;
      

        $sql = 'UPDATE `tbl_menu` SET `nom_plat`=:nom_plat, `prix_plat`=:prix_plat, `desc_plat`=:desc_plat, `type_plat`=:type_plat ,`photo_plat`=:imageprod WHERE `id_plat`=:id_plat;';

        $query = $db->prepare($sql);

        $query->bindValue(':id_plat', $id_plat, PDO::PARAM_INT);
        $query->bindValue(':nom_plat', $nom_plat, PDO::PARAM_STR);
        $query->bindValue(':prix_plat', $prix_plat, PDO::PARAM_STR);
        $query->bindValue(':desc_plat', $desc_plat, PDO::PARAM_STR);
        $query->bindValue(':type_plat', $type_plat, PDO::PARAM_STR);
        $query->bindValue(':imageprod', $imageprod, PDO::PARAM_STR);

        $query->execute();

        $_SESSION['message'] = "Plat modifié";
        require_once('close.php');

        header('Location: index.php');
    }else{
        $_SESSION['erreur'] = "Le formulaire est incomplet";
    }
}

// Est-ce que l'id existe et n'est pas vide dans l'URL
if(isset($_GET['id']) && !empty($_GET['id'])){
    require_once('connect.php');

    // On nettoie l'id envoyé
    $id_plat = strip_tags($_GET['id']);

    $sql = 'SELECT * FROM `tbl_menu` WHERE `id_plat` = :id_plat;';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On "accroche" les paramètre (id)
    $query->bindValue(':id_plat', $id_plat, PDO::PARAM_INT);

    // On exécute la requête
    $query->execute();

    // On récupère le plat
    $plat = $query->fetch();

    // On vérifie si le produit existe
    if(!$plat){
        $_SESSION['erreur'] = "Cet id n'existe pas";
        header('Location: index.php');
    }
}else{
    $_SESSION['erreur'] = "URL invalide";
    header('Location: index.php');
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un plat</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <main class="container">
        <div class="row">
            <section class="col-12">
                <?php
                    if(!empty($_SESSION['erreur'])){
                        echo '<div class="alert alert-danger" role="alert">
                                '. $_SESSION['erreur'].'
                            </div>';
                        $_SESSION['erreur'] = "";
                    }
                ?>
                <h1>Modifier un plat</h1>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nom_plat">Nom du plat</label>
                        <input type="text" id="nom_plat" name="nom_plat" class="form-control" value="<?= $plat['nom_plat']?>">
                    </div>
                    <div class="form-group">
                        <label for="prix_plat">Prix </label>
                        <input type="text" id="prix_plat" name="prix_plat" class="form-control" value="<?= $plat['prix_plat']?>">
                    </div>
                  
                <!--desc-->

                
                <div class="form-floating">
                <label for="desc_plat">description du plat :</label>
                    <textarea class="form-control" placeholder="entre la description du plat" id="desc_plat" name="desc_plat" style="height: 100px"><?=$plat['desc_plat']?></textarea>
                    
                </div>

                <!--desc-->
                  
                <!--type_plat--> 
                
                

                <?php
                        require_once('connect.php');
                        $sql = "SELECT * FROM  tbl_type_plat";
                        $query = $db->prepare($sql);
                        $query->execute();

                        $results = $query->fetchAll(PDO::FETCH_ASSOC);

                        if ($query->rowCount() > 0) { ?>
                        <div class="form-group">
                            <label for="type_plat">Type de plat</label>
                            <select class="custom-select" name="type_plat">
                        <option>Choisissez votre type de plat</option>
                        <?php foreach ($results as $row) { ?>
                        <option <?php if ($row['id_type_plat'] == $plat['type_plat'] ) {echo 'selected';} ?> value="<?php echo $row['id_type_plat']; ?>"><?php echo $row['nom_type_plat']; ?></option>
                        <?php } ?>
                        </select>
                        </div>
                        <?php } ?>


                <!--type_plat-->  


                        <!--photo_plat-->

                        <div class="form-group">
                        <label for="image">Image du produit</label>
                        <input type="file" id="image" name="image" class="form-control" value="<?= $plat['photo_plat']?>">
                        <input type="hidden" name="recup_nom_image" value="<?= $plat['photo_plat']?>">
                        </div>
                  

                        


                        <!--photo_plat-->



                    <input type="hidden" value="<?= $plat['id_plat']?>" name="id_plat">
                    <button class="btn btn-primary mt-4">Enregister</button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
