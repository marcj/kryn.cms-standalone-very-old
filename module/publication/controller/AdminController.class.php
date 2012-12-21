<?php


namespace Publication;

class AdminController {


    public function run(){


        $query = NewsQuery::create();

        $item = $query->findOneById(1);

        $item->setTitle('changed 222');

        $item->save();

        return $item;

        $query = NewsQuery::create();
        $query->select(array('Id', 'Title', 'Test.Title'));
        $query
        ->useTestQuery() // returns a new AuthorQuery instance
            ->filterByWorkspace() // this is an AuthorQuery method
        ->endUse();
        $item = $query->findOneById(1);

        var_dump($item);
        return $item;

        $item->setTitle('Hi was geht '.time());
        $item->save();
        return $item;
        $bla = '';

        $condition = array(

            array('id', '>', 0),
            'and',
            array('id', '<', 3),
            'or',
            array(
                array('id', '>', 5),
                'or',
                array('id', '<', 10),
                'and',
                array('id', '>', 10)
            )

        );
        $criteria = dbConditionToCriteria($condition, 'Publication\News');

        var_dump('done');
        var_dump($criteria->toString());

        $c = new \Criteria();
        $crit0 = $c->getNewCriterion(NewsPeer::ID, 5, \Criteria::GREATER_THAN);
        $crit1 = $c->getNewCriterion(NewsPeer::ID, 10, \Criteria::LESS_THAN);

// Perform AND at level 1 ($crit0 $crit1 )
        $crit0->addAnd($crit1);
        $crit2 = $c->getNewCriterion(NewsPeer::ID, 15, \Criteria::GREATER_THAN);
        $crit3 = $c->getNewCriterion(NewsPeer::ID, 20, \Criteria::LESS_THAN);
        $crit4 = $c->getNewCriterion(NewsPeer::ID, 100, \Criteria::GREATER_THAN);

// Perform AND at level 1 ($crit2 $crit3 $crit4 )
        $crit2->addAnd($crit3);
        $crit2->addAnd($crit4);

// Perform OR at level 0 ($crit0 $crit2 )
        $crit0->addOr($crit2);

// Remember to change the peer class here for the correct one in your model
        $c->add($crit0);

        //$result = TablePeer::doSelect($c);

        die($c->toString());





        exit;

        $query = NewsQuery::create();
        $item = $query->find();


        return $item;
    }

}

?>