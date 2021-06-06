
<?php
require_once 'vendor/autoload.php';

$connexion = new PDO('mysql:host=localhost;dbname=iwantit;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$faker = Faker\Factory::create();

//Permet de remove les quotes de l'email
function cleanMail($faker, $try = null)
{   
    $try = $faker->email;

    if(!preg_match("#['\"]#", $try)) 
    {
        return $try;
    } 
    else 
    {
        $try = $faker->email;
        return cleanMail($faker, $try);
    }
}

if(isset($_POST['submit']))
{   
    for($i=1; $i<=10; $i++)
    {
        $value[$i] = [];
        
        for($j=0; $j<100000; $j++)
        {
            $mail = cleanMail($faker);
            
            //on récupère le pattern avec un explode
            $getName = explode("@", $mail);
            $name = $getName[0];

            //on stocke les valeurs à insérer
            $value[$i][] = "('$name', '$mail')";
           
        }
        //on crée une chaine de caractère avec les valeurs stockées
        $imploValue[$i]= implode(', ',  $value[$i]);

        //variable intermédiaire
        $launch = $imploValue[$i];

        //on insere par chunk de 100000 rows en une requète
        $query = $connexion->prepare("INSERT INTO iwantit (nom, email) VALUES {$launch} ");
        $query->execute();
    }
}
?>
<form action="" method="POST">
    <input type="submit" name="submit">
</form>
