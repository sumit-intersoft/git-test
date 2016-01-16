<?php 
 $test = '14" chain';
 
 echo $test;
 echo "<br/>";
 echo htmlspecialchars($test);
?>


<input value="<?php echo addslashes($test); ?>"/>
<input value="<?php echo $test; ?>"/>