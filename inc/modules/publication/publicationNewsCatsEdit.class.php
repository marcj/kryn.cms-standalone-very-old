<?php

class publicationNewsCatsEdit extends windowEdit {

    public $table = 'publication_news_category';

    public $primary = array('rsn');

    public $multiLanguage = true;
    public $fields = array(
        'title' => array(
            'label' => 'Titel',
            'type' => 'text',
            'empty' => false
        ),
        'url' => array(
            'label' => 'Url',
            'type' => 'label',
            'modifier' => 'toModRewrite'
        )
    );
    
    public function toModRewrite( $p ){
        return kryn::toModRewrite( getArgv('title') );
    }
    
    public function saveItem(){
        parent::saveItem();
        kryn::invalidateCache('publicationCategoryList');
    }
}

?>
