  <?php

                 echo '<h3> PHP List All Server Variables</h3>';
foreach ($_SERVER as $key => $val) {
    echo $key.' '.$val.'<br/>';
}

// Display PHP INFO
echo phpinfo().'<br />';

?>



