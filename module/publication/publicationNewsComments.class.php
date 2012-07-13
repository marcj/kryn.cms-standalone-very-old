<?php

class publicationNewsComments extends adminWindowList {

    public $table = 'publication_comments';

    public $primary = 'id';

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


    public $addEntrypoint = '';

    public $add = 1;

    public $editEntrypoint = '';

    public $edit = 1;

    public $remove = 1;

    public $itemActions = array (
  0 => 
  array (
    'entrypoint' => 'admin/test/ficker$%,,df/Fg',
    'label' => 'Test',
    'icon' => '/admin/images/admin-files-list-icons.png',
  ),
);

    public $export = 0;


    function deleteItem() {

        $id = $_POST['item']['id'] + 0;

        $comment = dbExfetch('SELECT * FROM %pfx%publication_comments WHERE id = ' . $id);

        parent::deleteItem();

        $comments = dbExfetch(
            'SELECT count(*) as comcount FROM %pfx%publication_comments WHERE parent_id = ' . $comment['parent_id']);
        dbUpdate('publication_news', array('id' => $comment['parent_id']), array('commentscount' => $comments['comcount']));
    }

    function removeSelected() {
        // Get selected items
        $selection = json_decode(getArgv('selected'), 1);

        // Make a id chain
        $ids = "";
        foreach ($selection as $selected)
            $ids .= ", " . ($selected['id'] + 0);

        // Get parent id's
        $sql = "
            SELECT parent_id
            FROM %pfx%publication_comments
            WHERE id IN (" . substr($ids, 2) . ")
            GROUP BY parent_id
        ";
        $res = dbExfetch($sql, -1);

        // Remove selected
        parent::removeSelected();

        if ($res === false)
            return true; // TODO: [Ferdi] Something went wrong, but should we be concerned?

        // Update each parent
        foreach ($res as $parent)
        {
            $pid = $parent['parent_id'];
            $comments =
                dbExfetch("SELECT COUNT(*) as commCount FROM %pfx%publication_comments WHERE parent_id=$pid", 1);
            dbUpdate('publication_news', array('id' => $pid), array('commentscount' => $comments['commCount']));
        }

        return true;
    }
}
 ?>