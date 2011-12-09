<?php


class adminFS {

    public $magicFolderName = '';

    /**
     * @param $pPath
     */
    public function createFile($pPath, $pContent = false) {
        if (!file_exists('inc/template'.$pPath)){
            if (!$pContent)
                return touch('inc/template'.$pPath);
            else
                return kryn::fileWrite('inc/template'.$pPath, $pContent);
        }

        return false;
    }

    /**
     * @param $pPath
     */
    public function createFolder($pPath) {
        if (!file_exists('inc/template'.$pPath))
            return mkdir('inc/template'.$pPath, null, true);
        return false;
    }

    /**
     * @param $pPath
     * @param $pContent
     * @return bool
     */
    public function setContent($pPath, $pContent) {

        if (!file_exists($pPath) )
            $this->createFile($pPath);

        $fh = fopen('inc/template'.$pPath, 'w');
        $res = fwrite($fh, $pContent);
        fclose($fh);

        return $res===false?false:true;
    }


    /**
     * list directory contents
     *
     * Should return the item at $pPath with the informations:
     *  array(
     *  path => path to this file for usage in the administration and modules. Not the full http path. No trailing slash!
     *  name => basename(path)
     *  ctime => as unix timestamps
     *  mtime => as unix timestamps
     *  type => 'dir' or 'file'
     *  items => if it's a directory then here should be all files inside it, with the same infos above (except items)
     *  )
     * @param $pPath
     */
    public function getFiles($pPath){

        if (substr($pPath,0,1) != '/')
            $pPath = '/'.$pPath;

        $pPath = 'inc/template' . $pPath;
        $pPath = str_replace('..', '', $pPath);

        if (!file_exists($pPath))
            return false;

        $res['type'] = (is_dir($pPath)) ? 'dir' : 'file';
        $res['path'] = str_replace('inc/template', '', $pPath);

        if ($res['type'] == 'dir' && substr($pPath,-1) != '/')
            $pPath .= '/';


        if ($pPath == 'inc/template/')
            $res['name'] = '';
        else
            $res['name'] = basename($pPath);

        $res['ctime'] = filectime($pPath);
        $res['mtime'] = filemtime($pPath);

        if ($res['type'] == 'dir') {
            $h = opendir($pPath);

            $files = array();
            while ($file = readdir($h)) {
                if ($file == '.' || $file == '..') continue;
                $files[] = $file;
            }
            natcasesort($files);

            $items = array();
            foreach ($files as $file) {
                $path = $pPath . $file;

                $item['path'] = str_replace('inc/template', '', $pPath) . $file;
                $item['name'] = $file;
                $item['type'] = (is_dir($path)) ? 'dir' : 'file';
                $item['ctime'] = filectime($path);
                $item['mtime'] = filemtime($path);
                $res['items'][] = $item;
            }
        }

        return $res;
    }

    /**
     * @param $pPath
     */
    public function getFile($pPath){

        $pPath = 'inc/template'.$pPath;

        if(!file_exists($pPath))
            return false;

        $type = (is_dir($pPath))?'dir':'file';

        $name = basename($pPath);
        if($pPath == 'inc/template/')
            $name = '/';

        $ctime = filectime($pPath);
        $mtime = filemtime($pPath);

        return array(
            'path' => str_replace('inc/template', '', $pPath),
            'name' => $name,
            'type' => $type,
            'ctime' => $ctime,
            'mtime' => $mtime
        );
    }

    /**
     * disk usage
     *
     * @param $pPath
     */
    public function getSize($pPath){



    }

    /**
     * @param $pPath
     */
    public function fileExists($pPath){

        return file_exists('inc/template'.$pPath);
    }

    /**
     *
     * @param $pPathSource
     * @param $pPathTarget
     */
    public function copy($pPathSource, $pPathTarget){
        return copy('inc/template'.$pPathSource, 'inc/template'.$pPathTarget);
    }

    /**
     *
     * @param $pPathSource
     * @param $pPathTarget
     */
    public function move($pPathSource, $pPathTarget){

        return rename('inc/template'.$pPathSource, 'inc/template'.$pPathTarget);
    }

    /**
     *
     *
     * @param $pPath
     * @return bool|string
     */
    public function getContent($pPath){

        $pPath = 'inc/template'.$pPath;

        if (!file_exists($pPath)) return false;

        $handle = @fopen($pPath, "r");
        $fs = @filesize($pPath);

        if ($fs > 0)
            $content = @fread($handle, $fs);

        @fclose($handle);

        return $content;

    }

    public function search($pPath, $pPattern, $pDepth = -1){



    }

    /**
     *
     *
     * @param $pPath
     * @return bool|int
     */
    public function remove($pPath){

        //this filesystem layer moves the files to trash instead of real removing
        rename();

    }

}


?>