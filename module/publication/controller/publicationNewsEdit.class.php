<?php

class publicationNewsEdit extends adminWindowEdit {

    public $object = 'news';

    public $workspace = '0';

    public $multiLanguage = '1';

    public $multiDomain = '0';

    public $versioning = '1';


    public function saveItem() {

        $res = parent::saveItem();
        kryn::invalidateCache('publicationNewsList');

        return $res;
    }

    public function getUrl($pItem, $pPluginValues, $pPageRsn) {
        return kryn::toModRewrite($pItem['title']) . '/' . $pItem['id'];
    }

    public $fields = array (
        '__General__' => array (
          'label' => 'General',
          'type' => 'tab',
          'depends' => array (
            'title' => array (
              'label' => 'Title',
              'type' => 'text',
              'empty' => 0
            ),
            'category_id' => array (
              'label' => 'Category',
              'type' => 'select',
              'multiLanguage' => 'true',
              'empty' => 'false',
              'table' => 'publication_news_category',
              'table_label' => 'title',
              'table_key' => 'id',
            ),
            'tags' => array (
              'label' => 'Tags',
              'type' => 'text',
            ),
            'introimage' => array (
              'label' => 'Intro image',
              'type' => 'object',
              'object' => 'file',
              'objectOptions' => array (
                'selectionOnlyFiles' => 'true',
              ),
            ),
            'introimage2' => array (
              'label' => 'Intro image 2',
              'type' => 'fileChooser',
            ),
          ),
        ),
        '__Access__' => array (
          'type' => 'tab',
          'label' => 'Access',
          'depends' => array (
            'releaseat' => array (
              'label' => 'Release at',
              'desc' => 'If you want to release the news now, let it empty',
              'type' => 'datetime',
            ),
            'releasedate' => array (
              'label' => 'News date',
              'type' => 'datetime',
              'empty' => 'false',
            ),
            'deactivate' => array (
              'label' => 'Hide',
              'type' => 'checkbox',
            ),
            'deactivatecomments' => array (
              'label' => 'Deactivate comments (override plugin properties)',
              'type' => 'checkbox',
            ),
          ),
          'key' => '__Access__',
        ),
        '__Intro__' => array (
          'type' => 'tab',
          'label' => 'Intro',
          'depends' => array (
            'intro' => array (
              'label' => 'Intro',
              'type' => 'layoutelement',
            ),
          ),
        ),
        '__Content__' => array (
          'type' => 'tab',
          'label' => 'Content',
          'depends' => array (
            'content' => array (
              'label' => 'Content',
              'type' => 'layoutelement',
            ),
          ),
        ),
        '__Files__' => array (
          'type' => 'tab',
          'label' => 'Files',
          'depends' => array (
            'files' => array (
              'label' => 'Files',
              'type' => 'fileList',
              'size' => 10,
              'width' => 500,
            ),
          ),
        ),
        '__Comments__' => array (
          'type' => 'tab',
          'label' => 'Comments',
          'depends' => array (
            'comments' => array (
              'label' => 'Comments',
              'type' => 'window_list',
              'window' => 'publication/news/comments',
              'height' => 300,
            ),
          ),
        ),
      );

}
 ?>