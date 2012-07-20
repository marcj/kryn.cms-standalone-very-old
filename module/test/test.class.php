<?php

class test {

    function __construct(){

        return;
/*
        $s1 = new SystemPage();
        $s1->setDomainId(3);
        $s1->makeRoot();
        $s1->setTitle('Root 1');
        $s1->save();

        $sub = new SystemPage();
        $sub->setTitle('Sub #1 of Root 2');
        $sub->insertAsFirstChildOf($s1);
        $sub->save();

        $sub = new SystemPage();
        $sub->setTitle('Sub #2 of Root 2');
        $sub->insertAsFirstChildOf($s1);
        $sub->save();*/
/*
        $s3 = new SystemPage();
        $s3->setDomainId(3);
        $s3->insertAsFirstChildOf(SystemPageQuery::create()->findPk(9));
        $s3->setTitle('hihi');
        $s3->save();*/

        print "<pre>";
        $root = SystemPageQuery::create()->findRoot(3);
        $children = SystemPageQuery::create()->branchOf($root)->findTree(3);
        foreach ($children as $node) {
            echo str_repeat(' ', $node->getLevel()) . $node->getTitle() . "\n";
        }
        exit;
        foreach ($root->getBranch() as $node) {
            echo str_repeat(' ', $node->getLevel()) . $node->getTitle() . "\n";
        }
        exit;
        $root = SystemPageQuery::create()->findRoot(3);
        var_dump($root);
        exit;

    }

}