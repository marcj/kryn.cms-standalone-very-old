<?php


class adminStoreExtensions extends adminStore {


    public function getItem($pId) {

        $res = array();
        $config = kryn::$configs[$pId];

        $title = $config['title'][$lang];
        if (!$title)
            $title = $config['title']['en'];

        $res['label'] = $title;
        $res['id'] = $pId;

        return $res;
    }

    public function getItems($pFrom = 0, $pCount = 0) {

        global $client;

        $res = array();
        $lang = $client->getLang();

        $search = strtolower(getArgv('search', 1));


        foreach (kryn::$configs as $extId => $config) {

            $title = $config['title'][$lang];
            if (!$title)
                $title = $config['title']['en'];


            if ($search && strtolower(substr($title, 0, strlen($search))) != $search)
                continue;

            $res[$extId] = $title;
        }

        return $res;
    }

}

?>