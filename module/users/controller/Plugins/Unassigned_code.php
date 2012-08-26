<?php


    function content(){
        global $lang;

        switch($_REQUEST['param1']){
            case $lang['user_changepassword_tag']:
                return $this->changePasswort();
                break;
            case 'users-query':
                return $this->usersearch();
            default:
                return "mitgliederliste";
        }
    }

    function admin(){
        switch( getArgv(3) ){
        case 'browser':
            json( $this->browser() );
        case 'users':
            switch( getArgv(4) ){
                case 'resizeImg':
                    json( self::resizeImg(getArgv('path')) );
                case 'list':
                    $content = $this->userList();
                    break;
                case 'new':
                    $content = $this->userNew();
                    break;
                case 'fields':
                    $content = $this->fields();
                    break;
                case 'edit':
                    $content = $this->userEdit();
                    break;
                case 'groups':
                    $content = $this->groups();
                    break;
                case 'userGroups':
                    $content = $this->userGroups();
                    break;
            }
            break;
        case 'acl':
            require( PATH_MODULE.'users/usersAcl.class.php' );
            return usersAcl::init();
        }
        return $content;
    }
    
    public static function resizeImg( $pPath ){
        
        $res = resizeImageCached($pPath, '100x100', true);
        
        return $res;
    }

    /**
     * Returns the username or a list of users/gorups for the user/group dialog in the administration
     * admin/users/browser
     *
     * @return string
     */
    function browser(){
        
        $where = '';
        if( getArgv(4) == 'getName' ){
            $where = 'AND id = '.(getArgv('id')+0);
        }
        
        $type = getArgv('type', 3);
        $query = str_replace('*', '%', getArgv('query', 1));

        if( $type == 'users' || $type == 2 )
            $sql = "SELECT id, first_name, last_name, username, username as name FROM %pfx%system_user WHERE
            id > 0 AND (
            	username LIKE '$query%' OR first_name LIKE '$query%' OR username LIKE '$query%'
            ) $where";
        else
            $sql = "SELECT max(g.id) as id, max(g.name) as name, count(ga.group_id) as usercount FROM %pfx%system_groups g
            LEFT OUTER JOIN  %pfx%system_groupaccess ga ON (ga.group_id = g.id) WHERE  (
            	g.name LIKE '$query%'
            ) $where
            GROUP BY g.id";
            
        $sql .= " LIMIT 15";
        
        if( getArgv(4) == 'getName' ) 
            return dbExfetch($sql, 1);
        else
            return dbExfetch($sql, -1);
    }
    
	function cleanMsgs($pMsg) {
        	
        	$pMsg['message_text'] = str_replace('\r', '', $pMsg['message_text']);
        	$pMsg['message_text'] = str_replace('\n', "\n", $pMsg['message_text']);
        	
        	return $pMsg;        
     }    
    
    function pluginMessageSystemInbox($pConf){
    	global $user;      	
    	//check if user is logged in
    	if(!isset($user->user_id) || $user->user_id < 1)
    		return 'not logged in';

    	//check if aj request to mark as read
    	if(getArgv('ajSetRead') && getArgv('ajMessageRsn')) {
    		dbUpdate('user_messages', array('id'=>getArgv('ajMessageRsn', 1), 'user_id_to'=>$user->user_id), array('message_state' => '1'));
    		json(1);  		
    	}
    	
    	// aj reMessage
    	if(getArgv('ajSendReMessage') && getArgv('ajMessageRsn')) {
    		$subject = 'Re: ';
    		$msgAdding = '';
    		
    		// get message to respond to
    		$arOldFetch = dbExfetch("SELECT UM.*, SU.username AS user_name_from
        					FROM `%pfx%user_messages` UM 
        					JOIN `%pfx%system_user` SU 
        					WHERE 
        						UM.user_id_from = SU.id
        						AND UM.user_id_to = ".$user->user_id."
        						AND UM.id = ".getArgv('ajMessageRsn', 1)."
        					ORDER BY UM.send_tstamp DESC", 1);
        	if($arOldFetch) {        	
	        	$msgAdding = "\n\n\n------------------------\n\nFrom: ";	        		
	        	$msgAdding .= $arOldFetch['user_name_from']."\nDate: ".date('Y.m.d H:i', $arOldFetch['send_tstamp']);
		       	$msgAdding .= "\nSubject: ".$arOldFetch['message_subject']."\n\n".$arOldFetch['message_text'];	         	
	
	        	// adding subject	       
	       		$subject .= esc($arOldFetch['message_subject']);    			
	       		$reText = getArgv('ajResponseText', 1);
	       		
    			
    			//if attach re message
    			if(isset($pConf['attachReMessage']) && $pConf['attachReMessage'] == 1) {
    				$reText .= $msgAdding;
    			}   		
    		
    			$lastRsn =dbInsert( 'user_messages', array( 
            			'user_id_from' => $user->user_id,
            			'user_id_to' => $arOldFetch['user_id_from'],
            			'message_text' => $reText,
            			'message_state' => '0',
            			'send_tstamp' => mktime(),
            			'message_subject' => $subject            	
            		));

            	// send reminder email	
        		if(isset($pConf['sendReminder']) && $pConf['sendReminder'] == 1) {            		
            			//get mail address
            			$emailAddress = dbExFetch("SELECT email FROM `%pfx%system_user` WHERE id = ".$arOldFetch['user_id_from']." AND email LIKE '%@%' AND email LIKE '%.%'", 1);
            			if($emailAddress) {
            				$emailAddress = $emailAddress['email'];            			
            			
            				if($pConf['sendReminderSubject'] == '')
            					$pConf['sendReminderSubject'] = 'New Message';         				
            					
            				$toUserDetails = dbExFetch("SELECT 
            							username, 
            							email, 
            							first_name, 
            							last_name
            							FROM `%pfx%system_user` WHERE id =".$user->user_id, 1);
            				
            				tAssign('toUsers', $toUserDetails);
            				tAssign('sendReminder_message_text', getArgv('ajResponseText').$msgAdding);
            				tAssign('sendReminder_message_subject', $subject);
            				tAssign('sendReminder_to', $to);    
            				        				
            				kryn::sendMail($emailAddress, $pConf['sendReminderSubject'], tFetch('users/messageSystem/newMessageEmails/'.$pConf['sendReminderTemplate'].'.tpl'), "message-system@".$_SERVER['SERVER_NAME']);
            			}
            	} // end reminder email	
	
            	
            	if($lastRsn) {
            		json(1);            		
            	}
        	}    	
    	
    		json(0);
    	} // aj re message
    	
    	kryn::addCss('users/messageSystem/css/'.$pConf['template'].'.css');
        kryn::addJs('users/messageSystem/js/'.$pConf['template'].'.js');
        
        
        //check for action
        if(getArgv('action') || getArgv('action_select')) {
        	$action = getArgv('action_select');
        	$arMessageRsn = getArgv('one-message-action');        	
        	switch($action) {
        		case 'delete' :
        			foreach($arMessageRsn as $id) {
        				 dbUpdate('user_messages', array('id'=>$id, 'user_id_to'=>$user->user_id), array('message_state' => '2' ));
        				 tAssign('msg_deleted', true);
        			}
        		break;
        		
        		case 'flagRead':
        			foreach($arMessageRsn as $id) {
        				 dbUpdate('user_messages', array('id'=>$id, 'user_id_to'=>$user->user_id), array('message_state' => '1' ));
        				 tAssign('msg_flagged_read', true);
        			}
        		break;
        		
        		case 'flagUnRead' :
        			foreach($arMessageRsn as $id) {
        				dbUpdate('user_messages', array('id'=>$id, 'user_id_to'=>$user->user_id), array('message_state' => '0' ));
        				tAssign('msg_flagged_unread', true);
        			}
        		break;        	
        	}	
        }

      	//pages
        $page = getArgv('e1')+0;
        $page = ($page==0)?1:$page;       
        
        
        $resultsPerPage = 5;
        if($pConf['displayMessagesPerPage'])
        	$resultsPerPage = $pConf['displayMessagesPerPage'];       
        
    	if($page == 1)
            $offset = 0;
        else
            $offset = ($resultsPerPage * $page) - $resultsPerPage;        
             
        $totalResults = dbExfetch("SELECT COUNT(*) AS messageCount
        						FROM `%pfx%user_messages` UM 
        						JOIN `%pfx%system_user` SU 
        						WHERE 
        							UM.user_id_from = SU.id
        							AND UM.user_id_to = ".$user->user_id."
        							AND UM.message_state !='2'
        						ORDER BY UM.send_tstamp DESC", 1);            
        $count = $totalResults['messageCount'];        
        
        
        $pages = 1;
        if( $count > 0 && $resultsPerPage > 0 )
            $pages = ceil($count/ $resultsPerPage );
            
        //check if page too high
        if($offset > $count ) {
        	$page = ceil($count/$resultsPerPage);
        	$offset = ($page-1)*$resultsPerPage;
        }
        	

        tAssign( 'count', $count );   
        tAssign( 'pages', $pages );
        tAssign( 'currentMessagePage', $page );      
		// pages end
        
        $messagesIn = dbExfetch("SELECT UM.*, SU.username AS user_name_from
        						FROM `%pfx%user_messages` UM 
        						JOIN `%pfx%system_user` SU 
        						WHERE 
        							UM.user_id_from = SU.id
        							AND UM.user_id_to = ".$user->user_id."
        							AND UM.message_state !='2'
        						ORDER BY UM.send_tstamp DESC LIMIT ".$offset.",".$resultsPerPage, DB_FETCH_ALL);        
      
        
       $messagesIn = array_map(array($this, 'cleanMsgs'), $messagesIn);      
        
        tAssign('messages', $messagesIn); 
        
        if($pConf['newMessagePage']) {
        	tAssign('newMessagePage', $pConf['newMessagePage']);
        }        
  
    	return kryn::unsearchable(tFetch( 'users/messageSystem/inbox/'.$pConf['template'].'.tpl' ));
    }
    
    function pluginMessageSystemOutbox($pConf) {
    	global $user;      	
    	//check if user is logged in
    	if(!isset($user->user_id) || $user->user_id < 1)
    		return 'not logged in';
    	
    	
    	kryn::addCss('users/messageSystem/css/'.$pConf['template'].'.css');
        kryn::addJs('users/messageSystem/js/'.$pConf['template'].'.js');       

        //pages
        $page = getArgv('e1')+0;
        $page = ($page==0)?1:$page;       
        
        $resultsPerPage = 5;
        if($pConf['displayMessagesPerPage'])
        	$resultsPerPage = $pConf['displayMessagesPerPage'];       
        
    	if($page == 1)
            $offset = 0;
        else
            $offset = ($resultsPerPage * $page) - $resultsPerPage;        
             
        $totalResults = dbExfetch("SELECT COUNT(*) AS messageCount
        						FROM `%pfx%user_messages` UM 
        						JOIN `%pfx%system_user` SU 
        						WHERE 
        							UM.user_id_to = SU.id
        							AND UM.user_id_from = ".$user->user_id."
        						ORDER BY UM.send_tstamp DESC", 1);            
        $count = $totalResults['messageCount'];        
        
        
        $pages = 1;
        if( $count > 0 && $resultsPerPage > 0 )
            $pages = ceil($count/ $resultsPerPage );
            
        //check if page too high
        if($offset > $count ) {
        	$page = ceil($count/$resultsPerPage);
        	$offset = ($page-1)*$resultsPerPage;
        }
        	

        tAssign( 'count', $count );   
        tAssign( 'pages', $pages );
        tAssign( 'currentMessagePage', $page );      
		// pages end
        
        
        $messagesIn = dbExfetch("SELECT UM.*, SU.username AS user_name_to
        						FROM `%pfx%user_messages` UM 
        						JOIN `%pfx%system_user` SU 
        						WHERE 
        							UM.user_id_to = SU.id
        							AND UM.user_id_from = ".$user->user_id."
        						ORDER BY UM.send_tstamp DESC LIMIT ".$offset.",".$resultsPerPage, DB_FETCH_ALL);        
      
        
       $messagesIn = array_map(array($this, 'cleanMsgs'), $messagesIn);      
        
        tAssign('messages', $messagesIn); 
        
        if($pConf['newMessagePage']) {
        	tAssign('newMessagePage', $pConf['newMessagePage']);
        }        
  		tAssign('showMessageState', $pConf['showReciInfo']);
    	return kryn::unsearchable(tFetch( 'users/messageSystem/outbox/'.$pConf['template'].'.tpl' ));
    }
    
    function pluginMessageSystemNew($pConf) {    
    	global $user;      	
    	//check if user is logged in
    	if(!isset($user->user_id) || $user->user_id < 1)
    		return 'not logged in';   
    	
    	kryn::addCss('users/messageSystem/css/'.$pConf['template'].'.css');
        kryn::addJs('users/messageSystem/js/'.$pConf['template'].'.js');        
        
        //if message should be send
        if(getArgv('sendNewMessage')) {
            //first check id or username
            $to = getArgv('to_user_id', 1);
            $toUserRsn = false;
            if(is_numeric($to)) {	//id
            	$count = dbExFetch("SELECT COUNT(*) AS user_count FROM `%pfx%system_user` WHERE id =".$to);
            	if($count['user_count'] == 1){
            		$toUserRsn = $to;
            	}
            }else if(strpos($to, '@') !== false && strpos($to, '.') !== false){ // email
            $id = dbExFetch("SELECT id FROM `%pfx%system_user` WHERE email ='".$to."'", 1);
            	if(isset($id['id'])) {
            		$toUserRsn = $id['id'];
            	}
            }else{ // username
            	$id = dbExFetch("SELECT id FROM `%pfx%system_user` WHERE username ='".$to."'", 1);
            	if(isset($id['id'])) {
            		$toUserRsn = $id['id'];
            	}
            }
            
            if($toUserRsn) {
            		$lastRsn =dbInsert( 'user_messages', array( 
            			'user_id_from' => $user->user_id,
            			'user_id_to' => $toUserRsn,
            			'message_text' => getArgv('message_text', 1),
            			'message_state' => '0',
            			'send_tstamp' => mktime(),
            			'message_subject' => getArgv('message_subject', 1)            	
            		));   		
            		
            		
            		//email notification
            		if($pConf['sendReminder'] == 1) {            		
            			//get mail address
            			$emailAddress = dbExFetch("SELECT email FROM `%pfx%system_user` WHERE id = ".$toUserRsn." AND email LIKE '%@%' AND email LIKE '%.%'", 1);
            			if($emailAddress) {
            				$emailAddress = $emailAddress['email'];            			
            			
            				if($pConf['sendReminderSubject'] == '')
            					$pConf['sendReminderSubject'] = 'New Message';         				
            					
            				$toUserDetails = dbExFetch("SELECT 
            							username, 
            							email, 
            							first_name, 
            							last_name
            							FROM `%pfx%system_user` WHERE id =".$user->user_id, 1);
            				
            				tAssign('toUsers', $toUserDetails);
            				tAssign('sendReminder_message_text', getArgv('message_text'));
            				tAssign('sendReminder_message_subject', getArgv('message_subject'));
            				tAssign('sendReminder_to', $to);
            				
            				kryn::sendMail($emailAddress, $pConf['sendReminderSubject'], tFetch('users/messageSystem/newMessageEmails/'.$pConf['sendReminderTemplate'].'.tpl'), "message-system@".$_SERVER['SERVER_NAME']);
            			}
            		}
            		//email notification
            		       		
            		
            		if($lastRsn) 
            			tAssign('msg_message_sent', true);
            			
            }else{            
            	tAssign('msg_unknown_user', true);
            }
        
        }
        
        //check if attachment message is enabled
        if(($pConf['attachReMessage'] == 1 || getArgv('type') == 'fwd') && getArgv('oldMessageRsn', 1) > 0  ) {
            // load old Message
            $arOldFetch = dbExfetch("SELECT UM.*, SU.username AS user_name_from
                                FROM `%pfx%user_messages` UM
                                JOIN `%pfx%system_user` SU
                                WHERE
                                    UM.user_id_from = SU.id
                                    AND UM.user_id_to = ".$user->user_id."
                                    AND UM.id = ".getArgv('oldMessageRsn', 1)."
                                    AND UM.message_state !='2'
                                ORDER BY UM.send_tstamp DESC", 1);
            if($arOldFetch) {
                $arOldFetch = $this->cleanMsgs($arOldFetch);


                $msgAdding = "\n\n\n------------------------\n\nFrom: ";
                $msgAdding .= $arOldFetch['user_name_from']."\nDate: ".date('Y.m.d H:i', $arOldFetch['send_tstamp']);
                $msgAdding .= "\nSubject: ".$arOldFetch['message_subject']."\n\n".$arOldFetch['message_text'];
                $_REQUEST['message_text'] .= $msgAdding;

                // adding subject
                $_REQUEST['message_subject'] = "Re: ".$arOldFetch['message_subject'];
                $_REQUEST['to_user_id'] = $arOldFetch['user_id_from'];
                //if forward
                if(getArgv('type') == 'fwd') {
                    $_REQUEST['message_subject'] = "Fwd: ".$arOldFetch['message_subject'];
                    $_REQUEST['to_user_id'] = '';
                }
            }
        }


        if(getArgv('to') > 0 || getArgv('e1') > 0) {
            $toRsn = getArgv('to', 1);

            if($toRsn < 1 && getArgv('e1') > 0)
                $toRsn = getArgv('e1', 1);

            $userName = dbExFetch("SELECT username FROM `%pfx%system_user` WHERE id = ".$toRsn);
            if(isset($userName['username']))
                $_REQUEST['to_user_id'] = $userName['username'];
        }


        return kryn::unsearchable(tFetch( 'users/messageSystem/newMessage/'.$pConf['template'].'.tpl' ));
    }
    
    
    
    function pluginMessageSystemCountNew($pConf) {
    	global $user;
    	//check if user is logged in
    	if(getArgv('ajGetCount') && (!isset($user->user_id) || $user->user_id < 1))
    		json(0);
    	
    	
    	if(!isset($user->user_id) || $user->user_id < 1)
    		return 'not logged in';   
    	
    	kryn::addCss('users/messageSystem/css/'.$pConf['template'].'.css');
        kryn::addJs('users/messageSystem/js/'.$pConf['template'].'.js');
        
        $messageCount = dbExfetch("SELECT COUNT(*) AS newCount
        						FROM `%pfx%user_messages`      						
        						WHERE 

        						message_state ='0'
        						AND user_id_to = ".$user->user_id."
        						", 1);     
		
        if(getArgv('ajGetCount')) {
        	json($messageCount['newCount']+0);
        }
        
      	tAssign('newMessageCount', $messageCount['newCount']);        
       	if($pConf['InboxMessagePage']) {
       			tAssign('InboxMessagePage', $pConf['InboxMessagePage']);
      	 }  	
   		return kryn::unsearchable(tFetch( 'users/messageSystem/countNew/'.$pConf['template'].'.tpl' ));
    
    }
    
    
    
    public function manipulateLastFailedLoginsRow( $row ){
    
        $row[ 2 ] = preg_replace('/^SECURITY Login failed for \'(.*)\' to .*/', '$1', $row[2]);
        
        return $row;
    
    }
    
    public static function pluginRegistration( $pConf )
    {
        // Get template name from config
        $template = $pConf['template'];
        
        // Build list of required and hidden fields from config
        $required = array();
        foreach($pConf['required'] as $req)
            $required[$req] = true;
        $required['email'] = true;
        $required['password'] = true;
        
        $hidden = array();
        foreach($pConf['hidden'] as $hide)
        {
            $hidden[$hide] = true;
            if(isset($required[$hide]))
                unset($required[$hide]);
        }
        
        // Assign required and hidden fields to template
        tAssign('required', $required);
        tAssign('hidden', $hidden);
        
        // Handle JS call
        if(getArgv('postdata') == 1)
        {
            // Check if required fields are entered
            foreach($required as $req=>$val)
            {
                if(getArgv($req, 1) == "")
                    json(array("error" => _l('All required fields need to be filled')));
            }
            
            // Check if email address is correctly formatted
            if(!preg_match('/^'.self::getRegExpEmail().'$/i', getArgv('email')))
                json(array("error" => _l('Enter a valid email address')));
            
            if(self::emailAlreadyExists(getArgv('email')))
                json(array("error" => _l('An account with this email address already exists')));
            
            // Is account activated from the start?
            $active = 0;
            if($pConf['activation'] == 'now')
                $active = 1;
            
            // If activation is done by email, generate key
            $actKey = "";
            if($pConf['activation'] == 'email' || $pConf['activation'] == 'emailadmin')
                $actKey = self::generateActKey();
            
            // Create array of values to be inserted into database
            $values = array(
                "email" => getArgv('email'),
                "passwd" => md5(getArgv('password')),
                "username" => getArgv('username', 1),
                "first_name" => getArgv('firstname', 1),
                "last_name" => getArgv('lastname', 1),
                "street" => getArgv('street', 1),
                "city" => getArgv('city', 1),
                "zip" => getArgv('zipcode', 1),
                "country" => getArgv('country', 1),
                "phone" => getArgv('phone', 1),
                "fax" => getArgv('fax', 1),
                "company" => getArgv('company', 1),
                
                "activate" => $active,
                "activationkey" => $actKey,
                "created" => time()
            );
            
            // Insert into database
            dbInsert('system_user', $values);
            
            // For safety reasons, unset password field and replace html specialchars (prevent XSS)
            unset($values['passwd']);
            foreach( $values as &$value ){
            	$value = htmlspecialchars($value, 'UTF-8');
            }
            
            // Send activation email when required [use email and act key]
            if($actKey != "")
            { // Activation key set, thus send email
                $isSelfActivation = $pConf['activation'] == 'email';
                $eSubject = $isSelfActivation ? $pConf['email_subject'] : $pConf['emailadmin_subject'];
                $eFrom = $isSelfActivation ? $pConf['email_from'] : $pConf['emailadmin_from'];
                $eTemplate = $isSelfActivation ? $pConf['email_template'] : $pConf['emailadmin_template'];
                $sendTo = preg_replace( "/[\r\n]/", "", $values['email']);
                $actPage = $isSelfActivation ? $pConf['email_actpage'] : $pConf['emailadmin_actpage'];
                
                tAssign('values', $values);
                tAssign('actpage', $actPage);
                $body = tFetch("users/activateemail/$eTemplate.tpl");
                tAssign('values', null);
                
                mail($sendTo, '=?UTF-8?B?'.base64_encode($eSubject).'?=', $body, 'From: '. $eFrom."\r\n".'Content-Type: text/html; charset=utf-8');
            }
            
            // Send notification email when required
            if($pConf['notificationemail'] == 1)
            {
                $sendTo = $pConf['notifyemail_target'];
                $eSubject = $pConf['notifyemail_subject'];
                $eFrom = $pConf['notifyemail_from'];
                $eTemplate = $pConf['notifyemail_template'];
                
                tAssign('values', $values);
                $body = tFetch("users/notifyemail/$eTemplate.tpl");
                tAssign('values', null);
                
                mail($sendTo, '=?UTF-8?B?'.base64_encode($eSubject).'?=', $body, 'From: '. $eFrom."\r\n".'Content-Type: text/html; charset=utf-8');
            }
            
            // Registration completed
            json(array("href" => kryn::pageUrl($pConf['targetpage'])));
        }
        
        // Assign config to template 
        tAssign('pConf', $pConf);
        
        // Load template
        kryn::addJs("kryn/mootools-core.js");
        kryn::addJs("users/js/registration/$template.js");
        kryn::addCss("users/css/registration/$template.css");
        return kryn::unsearchable(tFetch("users/registration/$template.tpl"));
    }
    
    private static function getRegExpEmail(){
        // Regex written by James Watts and Francisco Jose Martin Moreno
        // http://fightingforalostcause.net/misc/2006/compare-email-regex.php
        return '([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*(?:[\w\!\#$\%\'\*\+\-\/\=\?\^\`{\|\}\~]|&amp;)+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)';
    }
    
    private static function emailAlreadyExists($email) {
        $email = esc($email);
        return dbExfetch("SELECT id FROM %pfx%system_user WHERE email='$email'", 1) != null;
    }
    
    private static function generateActKey(){
        $charSets = array();
        $charSets[] = array('count' => 4, 'chars' => "abcdefghijklmnopqrstuvwxyz");
        $charSets[] = array('count' => 4, 'chars' => "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
        $charSets[] = array('count' => 2, 'chars' => "0123456789");
        // Don't use these for auth key, would mess up the url, could be used for temporary password
        //$charSet[] = array('count' => 2, 'chars' => "!@#$+-*&?:"); 
        
        $temp = array();
        foreach($charSets as $cs)
        {
            $strLen = strlen($cs['chars']) - 1;
            for($i=0; $i<$cs['count']; $i++)
                $temp[] = $cs['chars'][rand(0, $strLen)];
        }
        
        shuffle($temp);
        return implode("", $temp);
    }
    
    public static function pluginActivation( $pConf )
    {
        $template = $pConf['template'];
        $result = array(
            'form' => false,
            'succes' => false,
            'failed' => false,
            'admin' => false,
        
        );
        
        // Get variables
        $email = getArgv('e', 1);
        $actKey = getArgv('k', 1);
        
        $data = array(
            'email' => htmlspecialchars($email),
            'actkey' => htmlspecialchars($actKey)
        );
        
        // If email and key are set, try to activate right away
        if($email == null || $actKey == null)
            $result['form'] = true;
        else
        {
            // Check activation key
            $sql = "
                SELECT id, activate
                FROM   %pfx%system_user
                WHERE  email = '$email' AND activationkey = '$actKey'
            ";
            klog('sql', $sql);
        
            $result = dbExfetch($sql, 1);
            if($result === false)
            { // Email/key combo not found!
                $result['form'] = true;
                $result['failed'] = true;
            }
            else 
            { // Email/key combo found, remove user activation key
                $id = $result['id'];
                $sql = "
                    UPDATE %pfx%system_user
                    SET activationkey = NULL
                    WHERE id=$id
                ";
                dbExec($sql);
                
                // User activation succes, set if admin needs to activate
                $result['succes'] = true;
                $result['admin'] = $result['activate'] == 0;
            }
        }
        
        tAssign('pConf', $pConf);
        tAssign('data', $data);
        tAssign('result', $result);
        
        kryn::addJs("users/js/activation/$template.js");
        kryn::addCss("users/css/activation/$template.css");
        return kryn::unsearchable(tFetch("users/activation/$template.tpl"));
    }