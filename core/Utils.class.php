<?php

namespace Core;

class Utils {


	/**
	 * Locks the process until the lock of $pId is released.
	 * If no $pId lock has been acquired it returns without waiting true.
	 *
	 * If this installation is in a cloud array, we store the lock
	 * into the current cache backend, so that other cloud buddies know
	 * that this id has been locked.
	 * 
	 * @param  string $pId
	 * @return boolean
	 */
	public static function lock($pId){

		
	}

	/**
	 * Tries to lock given id. If the id is already locked,
	 * the function returns without waiting for the mutex to be unlocked.
	 * 
	 * @param  string $pMutexId
	 * @return bool
	 */
	public static function tryLock($pId){


	}

	/**
	 * Releases a lock.
	 * 
	 * @param  [type] $pId [description]
	 * @return [type]      [description]
	 */
	public static function release($pId){


	}

}