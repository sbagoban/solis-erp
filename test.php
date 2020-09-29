<?php


$min = 1;
$max = 3;

$ad_ages = array("-1_-1", "0_3", "4_15");
$qty = array("0", "1", "2", "3");

$arr_arrays = array();
for($i = 0; $i < count($ad_ages); $i++)
{
    $_arr = array();
    
    for($j = 0; $j < count($qty); $j++)
    {
        $_arr[] = $ad_ages[$i] . ":" . $qty[$j];
    }
    
    $arr_arrays[] = $_arr;
}

$possible_combinations = generate_units_std_combinations($arr_arrays);
$possible_combinations = validate_units_standard_combinations($possible_combinations, $min, $max);

//reformat the combinations array
$arr_return = array();

for($i = 0; $i < count($possible_combinations); $i++)
{
    $combination = $possible_combinations[$i];
    
    $arr_return_combination = array();
    
    for($j = 0; $j < count($combination); $j++)
    {
        $pax = $combination[$j];
        
        $arr_details = explode(":", $pax);
        $age_ranges = $arr_details[0];
        $count = $arr_details[1];
        
        $arr_age_ranges = explode("_", $age_ranges);
        $age_from = $arr_age_ranges[0];
        $age_to = $arr_age_ranges[1];
        
        $arr_return_combination[] = array("AGEFROM"=>$age_from,"AGETO"=>$age_to, "No"=>$count);
    }
    
    $arr_return[] = $arr_return_combination;
}

return $arr_return;


print_r($possible_combinations);

function validate_units_standard_combinations($possible_combinations, $min, $max)
{
    $arr_final = array();
    
    //make sure that the sum_pax is within the min max ranges
    for($i = 0; $i < count($possible_combinations); $i++)
    {
        
        $arr_persons = $possible_combinations[$i];
        
        $sum_persons = 0;
        $arr_persons_final = array();
        for($j = 0; $j < count($arr_persons); $j++)
        {
            $pax = $arr_persons[$j]; //$pax in the form "AD:1"
            $arr_details = explode(":", $pax);
            $sum_persons += $arr_details[1];
            
            if($arr_details[1] > 0)
            {
                $arr_persons_final[] = $pax;
            }
        }
        
        if($sum_persons >= $min && $sum_persons <= $max)
        {
            $arr_final[] = $arr_persons_final;
        }
    }
    
    return $arr_final;
}


function generate_units_std_combinations($arrays, $i = 0) {
    if (!isset($arrays[$i])) {
        return array();
    }
    if ($i == count($arrays) - 1) {
        return $arrays[$i];
    }

    // get combinations from subsequent arrays
    $tmp = generate_units_std_combinations($arrays, $i + 1);

    $result = array();

    // concat each array from tmp with each element from $arrays[$i]
    foreach ($arrays[$i] as $v) {
        foreach ($tmp as $t) {
            $result[] = is_array($t) ? 
                array_merge(array($v), $t) :
                array($v, $t);
        }
    }

    return $result;
}


?>