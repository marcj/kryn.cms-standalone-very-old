<?php


class adminFS_AWS_S3 extends adminFS {

    private $config = array();

    private $aws;

    function __construct($pParams){

        require_once('inc/lib/amazonSdk/sdk.class.php');
        $this->config = $pParams;

        $this->aws = new AmazonS3($this->config['key'], $this->config['secret_key']);
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