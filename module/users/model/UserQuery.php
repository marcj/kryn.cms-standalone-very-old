<?php



/**
 * Skeleton subclass for performing query and update operations on the 'kryn_system_user' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.kryn
 */
class UserQuery extends BaseUserQuery {

    public function preSelect(PropelPDO $con){

    }

    public function withGroup(){
        $this->leftJoinUserGroup();
        $this->addJoin(\UserGroupPeer::GROUP_ID, \GroupPeer::ID, \Criteria::LEFT_JOIN);
        //$this->addAsColumn('Groups', 'string_agg('.\GroupPeer::NAME.', \',\')');
        $this->groupBy('Id');
        //$this->addSelectColumn('Groups');
        //$this->addAsColumn()
        //$this->select(array('Id', 'Username', 'Groups'));
    }

} // UserQuery
