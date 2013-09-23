<?php
$temp = array();
foreach ($branches as $branch) {
    $temp['id'] = $branch->id;
    $temp['name'] = $branch->name;
}
echo json_encode($temp);