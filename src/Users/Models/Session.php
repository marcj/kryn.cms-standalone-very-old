<?php

namespace Users\Models;

use Propel\Runtime\Connection\ConnectionInterface;
use Users\Models\Base\Session as BaseSession;

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
class Session extends BaseSession
{
    private $isStoredInDatabase = true;

    public function setIsStoredInDatabase($isStoredInDatabase)
    {
        $this->isStoredInDatabase = $isStoredInDatabase;
    }

    public function getIsStoredInDatabase()
    {
        return $this->isStoredInDatabase;
    }

    /**
     * {@inheritDoc}
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->getIsStoredInDatabase()) {
            parent::save($con);
        }
    }


    /**
     * {@inheritDoc}
     */
    public function getUser(ConnectionInterface $con = null)
    {
        $user = parent::getUser();
        if (!$user) {

            if (!$this->user_guest) {
                $this->user_guest = new User();
                $this->user_guest->setId(0);
                $this->user_guest->setUsername('Guest');
            }

            return $this->user_guest;
        }

        return $user;
    }

} // Session
