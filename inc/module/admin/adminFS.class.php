<?php


class adminFS {

    /**
     * @param $pPath
     */
    public function createFile($pPath) {
        if (!file_exists($pPath))
            touch($pPath);
    }

    /**
     * @param $pPath
     */
    public function createDir($pPath) {
        if (!file_exists($pPath))
            mkdir($pPath, null, true);
    }

    /**
     * @param $pPath
     * @param $pContent
     * @return bool
     */
    public function writeFile($pPath, $pContent) {

        if (!file_exists($pPath) )
            $this->createFile($pPath);

        $fh = fopen($pPath, 'w');
        $res = fwrite($fh,$pContent);
        fclose($fh);

        return $res===false?false:true;
    }


    /**
     * list directory contents
     *
     * @param $pPath
     */
    public function ls($pPath){

        if (substr($pPath,0,1) != '/')
            $pPath = '/'.$pPath;

        $pPath = 'inc/template' . $pPath;
        $pPath = str_replace('..', '', $pPath);

        if (!file_exists($pPath))
            return false;

        $res['type'] = (is_dir($pPath)) ? 'dir' : 'file';
        $res['path'] = str_replace('inc/template', '', $pPath);
        if ($pPath == 'inc/template/')
            $res['name'] = '';
        else
            $res['name'] = basename($pPath);

        $res['ctime'] = filectime($pPath);
        $res['mtime'] = filemtime($pPath);
        $res['writeaccess'] = krynAcl::checkAccess(3, $res['path'], 'write', true);

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

                $item['path'] = str_replace('inc/template', '', $path) . $file;
                $item['name'] = $file;
                $item['type'] = (is_dir($path)) ? 'dir' : 'file';
                $item['ctime'] = filectime($path);
                $item['mtime'] = filemtime($path);
                $item['writeaccess'] = krynAcl::checkAccess(3, $path, 'write', true);
                $res['items'][] = $item;
            }
        }

        return $res;
    }

    /**
     * disk usage
     *
     * @param $pPath
     */
    public function du($pPath){



    }

    /**
     *
     * @param $pPathSource
     * @param $pPathTarget
     */
    public function copy($pPathSource, $pPathTarget){



    }

    /**
     *
     * @param $pPathSource
     * @param $pPathTarget
     */
    public function move($pPathSource, $pPathTarget){



    }

    /**
     *
     *
     * @param $pPath
     * @return bool|string
     */
    public function readFile($pPath){

        if (!file_exists($pPath)) return false;

        $handle = @fopen($pPath, "r");
        $fs = @filesize($pPath);

        if ($fs > 0)
            $content = @fread($handle, $fs);

        @fclose($handle);

        return $content;

    }

    /**
     *
     *
     * @param $pPath
     * @param bool $pRecursive
     * @return bool|int
     */
    public function rm($pPath, $pRecursive = false){

        if (!file_exists($pPath)) return 2;

        if (isDir($pPath)){
            if( $pRecursive ) return delDir($pPath); else return rmdir($pPath);
        } else {
            return unlink($pPath);
        }

    }

}


?>