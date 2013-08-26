<?php

namespace Users\Controller;

use Core\Models\Base\AclQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Map\TableMap;
use RestService\Server;

class AdminController extends Server
{
    public function run($entryPoint = null)
    {
        $this->addGetRoute('acl/search', 'getSearch');
        $this->addGetRoute('acl', 'loadAcl');
        $this->addPostRoute('acl', 'saveAcl');
        $this->addGetRoute('test', 'test');

        return parent::run();
    }

	/**
     * Gets all rules from given type and id;
     *
     * @param  int       $type
     * @param  int       $id
     *
     * @return array|int
     */
    public function loadAcl($type, $id)
    {
        $type = ($type == 'user') ? 0 : 1;

        return AclQuery::create()
            ->filterByTargetType($type+0)
            ->filterByTargetId($id+0)
            ->orderByPrio(Criteria::DESC)
            ->find()
            ->toArray(null, null, TableMap::TYPE_STUDLYPHPNAME);

    }

    /**
     * Saves the given rules.
     *
     * @param  int   $targetId
     * @param  int   $targetType
     * @param  array $rules
     *
     * @return bool
     */
    public function saveAcl($targetId, $targetType, $rules)
    {
        $targetId += 0;
        $targetType += 0;

        dbDelete(
            'system_acl',
            array(
                 'target_type' => $targetType,
                 'target_id' => $targetId
            )
        );

        if (count($rules) == 0) {
            return true;
        }

        $i = 1;
        foreach ($rules as $rule) {

            unset($rule['id']);
            $rule['prio'] = $i;
            $rule['target_type'] = $targetType;
            $rule['target_id'] = $targetId;
            dbInsert('system_acl', $rule);
            $i++;
        }

        return true;
    }

    /**
     *
     * @internal
     *
     * @param $items
     * @param $type
     */
    public static function setAclCount(&$items, $type)
    {
        if (is_array($items)) {
            foreach ($items as &$item) {
                $item['ruleCount'] = self::load($type, $item['id'], true);
            }
        }
    }

    public static function load($type, $id, $asCount = false)
    {
        $where = 'target_type = ' . ($type + 0);
        $where .= ' AND target_id = ' . ($id + 0);

        $where .= " ORDER BY prio DESC";

        if (!$asCount) {
            return dbTableFetch('system_acl', DB_FETCH_ALL, $where);
        } else {
            return dbCount('system_acl', $where);
        }

    }

    /**
     * Search user and group.
     *
     * @return array array('users' => array, 'groups' => array())
     */
    public function getSearch()
    {
        $q = getArgv('q', 1);
        $q = str_replace("*", "%", $q);

        $userFilter = array();
        $groupFilter = array();

        if ($q) {
            $userFilter = array(
                array('username', 'like', "$q%"),
                'OR',
                array('first_name', 'like', "$q%"),
                'OR',
                array('last_name', 'like', "$q%"),
                'OR',
                array('email', 'like', "$q%"),
            );
            $groupFilter = array(
                array('name', 'like', "$q%")
            );
        }

        $users = \Core\Object::getList(
            'Users\\User',
            $userFilter,
            array(
                 'limit' => 10,
                 'fields' => 'id,username,email,groupMembership.name,firstName,lastName'
            )
        );

        self::setAclCount($users, 0);

        $groups = \Core\Object::getList(
            'Users\\Group',
            $groupFilter,
            array(
                 'fields' => 'name',
                 'limit' => 10
            )
        );

        self::setAclCount($groups, 1);

        json(
            array(
                 'users' => $users,
                 'groups' => $groups
            )
        );
    }

}
