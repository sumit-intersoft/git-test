<?php

"Product Type:Jewelry|"
. "Jewelry Type:Rings"
        . "|Material: Primary:Gold"
        . "|Material: Primary - Color:Two-Tone"
        . "|Material: Primary - Purity:10K"
        . "|Width of Item:2 to 11 mm (tapered)|"
        . "Sold By Unit:Each|"
        . "Stone Type_1:Cubic Zirconia (CZ)|"
        . "Stone Creation Method_1:Synthetic|"
        . "Stone Treatment_1:Synthetic";


$val = array();
$a[2] = 'sumitwerwre';


        for($i=1;$i<=6; $i++) {
            
          //  $test['abc']($a[$i] ? '['.$a[$i].']': '[]') = 'dsfdsd';
            
          //  $test['abc'][$a[$i]] = 'dsfdsd';
        }
echo '<pre>'; print_r($test); echo '</pre>';


exit;

//$test2= ' sdfd, dfsd';
//$arr = explode('|',$test2);
//echo '<pre>';
//print_r($arr);
//echo '</pre>';







$test ="Product Type:Jewelry|Jewelry Type:Necklaces|Chain Type:Rope Chains|Material: Primary:Gold|Material: Primary - Color:Yellow|Material: Primary - Purity:14K|Sold By Unit:Each|Chain Length:20 in|Chain Width:1.5 mm|Clasp /Connector:Lobste";
$arr = explode('|',$test);

foreach($arr as $value){
    
   $sep = strrpos($value, ":") ;
   if($sep !== false) {
//   echo  substr($value, 0,$sep);
//   echo " :: ";
//   echo substr($value,($sep+1));
//    echo "<br/>";    
    $val[substr($value, 0,$sep)] = substr($value,($sep+1));
   }
}

echo '<pre>'; print_r($val); echo '</pre>';

$test2 = "Solid;Diamond-cut;14k Yellow gold;Lobster;Special lengths avail.";
$result1= explode(';', $test2);
$val2=array_fill_keys($result1,"");



echo '<pre>'; 
$val = array_merge($val,$val2); 
print_r($val);
echo '</pre>';

exit;





$a = array(
    array(
        'id' => 2135,
        'first_name' => 'John',
        'last_name' => 'Doe',
    ),
    array(
        'id' => 3245,
        'first_name' => 'Sally',
        'last_name' => 'Smith',
    )
);

$b = array_column($a, null, 'id');
//echo '<pre>'; print_r($b); echo '</pre>';
//exit;
$data = array();

for($i=0;$i<=10;$i++) {
    
   $data['attributes'][] = array(
				'attribute_id'    => $i+1,
				'name'            => 'name_'. ($i+1),
				'attribute_group' => $i+1,
				'sort_order'      => $i+5,
				
    );
}


//$data['attributes'][] = array(
//				'attribute_id'    => 2,
//				'name'            => 'name_'. ($i+1),
//				'attribute_group' => $i+1,
//				'sort_order'      => $i+5,
//				
//    );
//echo '<pre>'; print_r($data['attributes']); echo '</pre>';

echo '<pre>'; print_r($data['attributes']); echo '</pre>';

$attribute_list_keys = array_column($data['attributes'], 'attribute_id');

$attribute_list_keys = array_flip($attribute_list_keys);

$data['attributes'][$attribute_list_keys[2]] = array(
				'attribute_id'    => 2,
				'name'            => 'name_'. ($i+1),
				'attribute_group' => $i+1,
				'sort_order'      => $i+5,
    );
echo '<pre>'; print_r($data['attributes']); echo '</pre>';


exit;














?>