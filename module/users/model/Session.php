<?php

namespace Users;

use Users\om\BaseSession;

/**
 * Skeleton subclass for representing a row from the 'kryn_system_session' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.kryn
 */
class Session extends BaseSession {

    private $isStoredInDatabase = true;

    public function setIsStoredInDatabase($isStoredInDatabase) {
        $this->isStoredInDatabase = $isStoredInDatabase;
    }

    public function getIsStoredInDatabase() {
        return $this->isStoredInDatabase;
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(\PropelPDO $con = null){
        if ($this->getIsStoredInDatabase()){
            parent::save($con);
        }
    }


    /**
     *
     *
     * @return User
     */
    public function getUser(PropelPDO $con = null, $doQuery = true){
        $user = parent::getUser();
        if (!$user){

            if (!$this->user_guest){
                $this->user_guest = new User();
                $this->user_guest->setId(0);
                $this->user_guest->setUsername('Guest');
            }

            return $this->user_guest;
        }

        return $user;
    }

} // Session
