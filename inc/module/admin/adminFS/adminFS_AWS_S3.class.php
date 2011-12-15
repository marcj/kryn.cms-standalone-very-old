<?php


class adminFS_AWS_S3 extends adminFS {

    private $config = array();

    private $aws;

    function __construct($pParams){

        require_once('inc/lib/amazonSdk/sdk.class.php');
        $this->config = $pParams;

        error_log(print_r($this->config,true));

        CFCredentials::set(array(
            '@default' => array(
                'key' => $this->config['key'],
                'secret' => $this->config['secret_key'],
                'default_cache_config' => 'cache/object/',
                'certificate_authority' => false
            )
        ));

        $this->aws = new AmazonS3();
    }

    public function createFolder($pPath){
        $response = $this->aws->create_object($this->config['bucket'], substr($pPath,1).'/', array(
            'acl' => AmazonS3::ACL_PUBLIC,
            'contentType' => 'text/plain'
        ));
        return $response->isOk();
    }

    public function createFile($pPath){
        $response = $this->aws->create_object($this->config['bucket'], substr($pPath,1), array(
            'acl' => AmazonS3::ACL_PUBLIC
        ));
        return $response->isOk();
    }


    public function setContent($pPath, $pContent) {
        $response = $this->aws->create_object($this->config['bucket'], substr($pPath,1), array(
            'acl' => AmazonS3::ACL_PUBLIC,
            'body' => $pContent
        ));
        return $response->isOk();
    }


    public function deleteFile($pPath){

        //delete subfiles
        $response2 = $this->aws->delete_all_objects($this->config['bucket'], '/^'.preg_quote(substr($pPath,1), '/').'\/.*/');

        //delete file
        $response = $this->aws->delete_all_objects($this->config['bucket'], '/^'.preg_quote(substr($pPath,1), '/').'/');
        return $response || $response2;
    }


    public function move($pPathSource, $pPathTarget){

        //do we have subfiles ?
        $response = $this->aws->get_object_list($this->config['bucket'], array(
            'pcre' => '/^'.preg_quote(substr($pPathSource,1), '/').'\/.*/'
        ));

        if (is_array($response) && count($response) > 0){
            foreach($response as $file){

                $newFile = substr($pPathTarget.'/'.substr($file, strlen($pPathSource)), 1);
                $this->aws->copy_object(
                    array(
                        'bucket'   => $this->config['bucket'],
                        'filename' => $file
                    ),
                    array(
                        'bucket'   => $this->config['bucket'],
                        'filename' => $newFile
                    )
                );
            }

            $files2Delete = array();
            foreach($response as $file){
                $files2Delete[] = array('key' => $file);
            }

            $this->aws->delete_objects($this->config['bucket'], array('objects' => $files2Delete));
        } else {

            $this->aws->copy_object(
                array(
                    'bucket'   => $this->config['bucket'],
                    'filename' => substr($pPathSource,1)
                ),
                array(
                    'bucket'   => $this->config['bucket'],
                    'filename' => substr($pPathTarget,1)
                )
            );
            $this->aws->delete_object($this->config['bucket'], substr($pPathSource,1));
        }

        return true;
    }

    public function copy($pPathSource, $pPathTarget){

        //do we have subfiles ?
        $response = $this->aws->get_object_list($this->config['bucket'], array(
            'pcre' => '/^'.preg_quote(substr($pPathSource,1), '/').'\/.*/'
        ));

        if (is_array($response) && count($response) > 0){
            foreach($response as $file){

                $newFile = substr($pPathTarget.'/'.substr($file, strlen($pPathSource)), 1);
                $this->aws->copy_object(
                    array(
                        'bucket'   => $this->config['bucket'],
                        'filename' => $file
                    ),
                    array(
                        'bucket'   => $this->config['bucket'],
                        'filename' => $newFile
                    )
                );
            }
        } else {

            $this->aws->copy_object(
                array(
                    'bucket'   => $this->config['bucket'],
                    'filename' => substr($pPathSource,1)
                ),
                array(
                    'bucket'   => $this->config['bucket'],
                    'filename' => substr($pPathTarget,1)
                )
            );
        }

        return true;
    }


    public function getPublicUrl($pPath){
        return $this->aws->get_object_url($this->config['bucket'], substr($pPath,1));
    }

    public function getContent($pPath){
        $response = $this->aws->get_object($this->config['bucket'], substr($pPath,1));
        return $response->body;
    }

    public function getFile($pPath){
        return $this->getFiles($pPath, true);
    }

    public function getFiles($pPath, $pIsFile = false){

        $items = array();
        $opts = array(
            'delimiter' => '/'
        );

        if ($pPath != '/' && !$pIsFile)
            $opts['prefix'] = substr($pPath,1).'/';

        $response = $this->aws->list_objects($this->config['bucket'], $opts);

        foreach($response->body->Contents as $file){
            $name = (string)$file->Key;

            if ($name == $opts['prefix']) continue;

            if ($opts['prefix'])
                $name = substr((string)$file->Key,strlen($opts['prefix']));

            $items[$name] = array(
                'name'  => $name,
                'type'  => 'file',
                'path'  => ($pIsFile)?$pPath:$pPath.($pPath=='/'?'':'/').$name,
                'size'  => (string)$file->Size,
                'mtime' => strtotime((string)$file->LastModified)
            );
            if ($pIsFile)
                return $items[$name];
        }

        if ($response->body->CommonPrefixes && !$pIsFile){
            //we maybe got subfolder
            foreach($response->body->CommonPrefixes as $file){
                $name = substr((string)$file->Prefix,strlen($opts['prefix']),-1);

                $items[$name] = array(
                    'name'  => $name,
                    'type'  => 'dir',
                    'path'  => $pPath.($pPath=='/'?'':'/').$name,
                    'size'  => 0,
                    'mtime' => 0
                );
            }
        }

        return $items;
    }


}


?>