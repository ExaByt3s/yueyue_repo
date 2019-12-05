<?php 
    
    echo 'test';
    echo "</br>";
    echo $ttest."eeee";
    dump($_GET);
    dump($_POST);
    dump($_SERVER);
    dump($_INPUT);


?>
<html>
    <head>
        <title>this is a test</title>
    </head>
    <body>
        <p>
            this is a p
        </p>
        <h5>
            this is a h5
        </h5>
        <?php for($i=0;$i<10;$i++):?>
            <?php echo $i."</br>";?>
        <?php endfor;?>
    </body>
</html>