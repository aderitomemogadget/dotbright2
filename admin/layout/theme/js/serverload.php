<?php
 //$load = sys_getloadavg();
//echo $load[0];

$loads=sys_getloadavg();
$core_nums=trim(shell_exec("grep -P '^physical id' /proc/cpuinfo|wc -l"));
$load=$loads[0]/$core_nums;
//echo $load;

//echo json_encode(array("result" => ($load[0]*100)));
echo json_encode(array("result" => ($load*100)));


?>