<?php

namespace Publication;

if (!defined('KRYN_MANAGER')) return false;


for ($i=1; $i<=5; $i++){
    $news = new News();
    $news->setTitle('News item #'.$i);
    $news->setIntro('intro '.$i);
    $news->save();
}