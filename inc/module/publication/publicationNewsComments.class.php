<?php

class publicationNewsComments extends adminWindowList {

    public $table = 'publication_comments';

    public $primary = 'rsn';

    public $multiLanguage = 0;

    public $multiDomain = 0;

    public $versioning = 0;

    public $columns = array (
  'owner_username' => 
  array (
    'label' => 'Name',
    'type' => 'text',
  ),
  'email' => 
  array (
    'label' => 'E-Mail',
    'type' => 'text',
  ),
  'ip' => 
  array (
    'label' => 'IP',
    'type' => 'text',
  ),
  'created' => 
  array (
    'label' => 'Created',
    'type' => 'datetime',
  ),
);

    public $itemsPerPage = 20;

    public $order = array (
  0 => 
  array (
    'field' => 'created',
    'direction' => 'asc',
  ),
);

    public $filter = array (
  0 => 'owner_username',
  1 => 'email',
);

    public $add = 1;

    public $edit = 1;

    public $remove = 1;

    public $export = 0;


    function deleteItem() {

        $rsn = $_POST['item']['rsn'] + 0;

        $comment = dbExfetch('SELECT * FROM %pfx%publication_comments WHERE rsn = ' . $rsn);

        parent::deleteItem();

        $comments = dbExfetch(
            'SELECT count(*) as comcount FROM %pfx%publication_comments WHERE parent_rsn = ' . $comment['parent_rsn']);
        dbUpdate('publication_news', array('rsn' => $comment['parent_rsn']), array('commentscount' => $comments['comcount']));
    }

    function removeSelected() {
        // Get selected items
        $selection = json_decode(getArgv('selected'), 1);

        // Make a rsn chain
        $rsns = "";
        foreach ($selection as $selected)
            $rsns .= ", " . ($selected['rsn'] + 0);

        // Get parent rsn's
        $sql = "
            SELECT parent_rsn
            FROM %pfx%publication_comments
            WHERE rsn IN (" . substr($rsns, 2) . ")
            GROUP BY parent_rsn
        ";
        $res = dbExfetch($sql, -1);

        // Remove selected
        parent::removeSelected();

        if ($res === false)
            return true; // TODO: [Ferdi] Something went wrong, but should we be concerned?

        // Update each parent
        foreach ($res as $parent)
        {
            $prsn = $parent['parent_rsn'];
            $comments =
                dbExfetch("SELECT COUNT(*) as commCount FROM %pfx%publication_comments WHERE parent_rsn=$prsn", 1);
            dbUpdate('publication_news', array('rsn' => $prsn), array('commentscount' => $comments['commCount']));
        }

        return true;
    }
}
 ?>