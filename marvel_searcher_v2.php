<form id="hero_form" name="heroes" method="POST">
    <input type="text" name="hero">
    <input type="submit" value="Search hero">
</form>

<?php

if ( isset($_POST['hero'])) {
	
    $content = str_replace(' ', '+', $_POST['hero']);
    $search_query =  $content;
    $PRIV_KEY = "YOUR_PRIVATE_KEY";
    $PUBLIC_KEY = "YOUR_PUBLIC_KEY";

// To create a new TimeStamp
    $date = new DateTime();
    $timestamp=$date->getTimestamp();
    
//Add your keys here. It would be better if you include them from an external file in production.
    $keys=$PRIV_KEY . $PUBLIC_KEY;
// Add the timestamp to the keys
    $string=$timestamp.$keys;
//Generate MD5 digest, also hash is faster than md5 function
    $md5=hash('md5', $string);
// create a new cURL resource
    $ch = curl_init();
// set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, "http://gateway.marvel.com:80/v1/public/characters?ts=$timestamp&apikey=$PUBLIC_KEY&hash=$md5&name=$search_query");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json')                                                                       
);   
// grab URL and pass it to the browser
 //Execute curl
    $output= curl_exec($ch) or die(curl_error()); 
//Format JSON output
    $results = json_decode($output, true);

if(!empty($results['data']['results'][0]['name'])){
    $hero_name= $results['data']['results'][0]['name'];
    $hero_description = $results['data']['results'][0]['description'];
    $main_image = $results['data']['results'][0]['thumbnail']['path'] . '.' . $results['data']['results'][0]['thumbnail']['extension'];


    echo "<h1>$hero_name</h1>";
    echo "<img src='"; echo $main_image; echo "'/>";
    echo "<h2>$hero_description</h2>";

    echo '<hr>';
    $comic_series = $results['data']['results'][0]['stories']['items'];

    ?>

    <ul>
       <?php
       foreach ($comic_series as $volumes){
           ?>
           <li><a href=" <?php echo $volumes['resourceURI']?>"><?php echo $volumes['name'] ?></a></li>
           <?php
       }
       ?>
   </ul>
   <?php

   curl_close($ch);

}
else{
    echo '<i>No se han encontrado resultados</i>';
}
}

?>
?>
