<?php

$search  = array('A','C');
$replace = array('B','D');
$subject = 'AC';
echo str_replace($search, $replace, $subject);