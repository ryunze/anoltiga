<?php

function modkurawal($txt, $data)
{
    
    $patt = "/{{(.*?)}}/i";

    $matches = [];
    preg_match_all($patt, $txt, $matches);

    foreach ($matches[1] as $i => $m) {
        $matches[1][$i] = trim($m);
    }

    foreach ($matches[0] as $i => $m) {
        if (array_key_exists($matches[1][$i], $data)) {
            $txt = str_replace($m, $data[$matches[1][$i]], $txt);
        }
    }

    return $txt;

}
