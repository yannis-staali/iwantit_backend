<?php

$connexion = new PDO('mysql:host=localhost;dbname=iwantit;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));


if(isset($_POST['submit']))
{   
    for($y=0; $y<=1000000; $y+=10000)
    {
        $value[$y] = [];
        $listId[$y] = [];

        //on récupère les données par chunk de 10000 rows
        $query2 = $connexion->prepare("SELECT email, id FROM iwantit LIMIT 10000 OFFSET {$y} ");
        $query2->execute();

        $result2 = $query2->fetchAll(PDO::FETCH_NUM);

        for($i=0; $i<count($result2); $i++)
        {
            $mail = $result2[$i][0];
            $id = $result2[$i][1];

            //on explode l'email pour récupérer le pattern
            $pat = explode("@", $mail);
            $pat2 =  explode(".", $pat[1]);
            $pattern = $pat2[0];

            //on stocke les valeurs
            $value[$y][] = "WHEN $id THEN '$pattern'";
            $listId[$y][] = $id;             
        }

        //on crée une chaine de caractères avec toutes les données
        $imploValue[$y]= implode(' ',  $value[$y]);
        $launch = $imploValue[$y];

        //on crée une chaine de caractère avec tous les id
        $imploId[$y]= implode(',',  $listId[$y]);
        $suiteId = $imploId[$y];

        //on lance l'update avec une transaction pour s'assurer de l'intégrité des données
        try
        {
            $connexion->beginTransaction();

             //on lance l'update par chunk de 10000 rows
            $query3 = $connexion->prepare("UPDATE iwantit SET email_provider= (CASE id {$launch} END) WHERE id IN ({$suiteId}) ");
            $query3->execute();
            
            $connexion->commit();
        } 
        catch (Exception $e)
        {
            $connexion->rollBack();
            echo 'Failed:' . $e->getMessage();
        }

        //on unset toutes les variables pour liberer la mémoire entre chaque boucle
        unset($value);
        unset($listId);
        unset($result2);
        unset($launch);
        unset($suiteId);

    }
 
}

?>
<form action="" method="POST">
    <input type="submit" name="submit">
</form>
