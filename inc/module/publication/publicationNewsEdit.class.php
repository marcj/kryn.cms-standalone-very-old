<?php

class publicationNewsEdit extends windowEdit {

    public $object = 'news';

    public $multiLanguage = true;
    public $versioning = true;

    public $previewPlugins = array(
        'newsDetail' => 'getUrl'
    );

    public function getUrl($pItem, $pPluginValues, $pPageRsn) {
        return kryn::toModRewrite($pItem['title']) . '/' . $pItem['rsn'];
    }

    public function saveItem() {

        $res = parent::saveItem();
        kryn::invalidateCache('publicationNewsList');

        return $res;
    }

    public $tabFields = array(
        'General' => array(
            'title' => array(
                'label' => 'Title',
                'type' => 'text',
                'empty' => false
            ),
            'category_rsn' => array(
                'label' => 'Category',
                'type' => 'select',
                'multiLanguage' => true,
                'empty' => false,
                'table' => 'publication_news_category',
                'table_label' => 'title',
                'table_key' => 'rsn'
            ),
            'tags' => array(
                'label' => 'Tags',
                'type' => 'text'
            ),
            'introimage' => array(
                'label' => 'Intro image',
                'type' => 'object',
                'object' => 'file',
                'objectOptions' => array(
                    'selectionOnlyFiles' => true
                )
            ),
            'introimage2' => array(
                'label' => 'Intro image 2',
                'type' => 'fileChooser'
            ),
        ),
        'Access' => array(
            'releaseat' => array(
                'label' => 'Release at',
                'desc' => 'If you want to release the news now, let it empty',
                'type' => 'datetime',
            ),
            'releasedate' => array(
                'label' => 'News date',
                'type' => 'datetime',
                'empty' => false
            ),
            'deactivate' => array(
                'label' => 'Hide',
                'type' => 'checkbox'
            ),
            'deactivatecomments' => array(
                'label' => 'Deactivate comments (override plugin properties)',
                'type' => 'checkbox'
            )
        ),
        'Intro' => array(
            'intro' => array(
                'label' => 'Intro',
                'type' => 'layoutelement'
            )
        ),
        'Content' => array(
            'content' => array(
                'label' => 'Content',
                'type' => 'layoutelement'
            )
        ),
        'Files' => array(
            'files' => array(
                'label' => 'Files',
                'type' => 'fileList',
                'size' => 10,
                'width' => 500
            )
        ),
        'Comments' => array(
            'comments' => array(
                'label' => 'Comments',
                'type' => 'window_list',
                'window' => 'publication/news/comments',
                'height' => 300
            )
        )
    );
}

?>
