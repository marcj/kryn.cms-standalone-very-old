<?php


krnTestsetInstallExtensionFromArray(
    'ts_postgresql',
    array(
        'db' => array(
            'postgresql' => array(
                'rsn' => array(
                    'int', '', 'DB_PRIMARY'
                ),
                'title' => array(
                    'varchar', '255'
                )
                ,
                'active' => array(
                    'boolean'
                ),

                'numberfloat' => array(
                    'float'
                )
            )
        )
    )
);

$columns = database::getOptions('postgresql');

if (!$columns || !$columns['title'])
    krynTestSetExit('Columns are not available.', __FILE__, __LINE__);

$primaryValue = dbInsert('postgresql', array('title' => 'Test 1', 'active' => 1, 'numberfloat' => 35.45));

if (!$primaryValue)
    krynTestSetExit('Can not insert row.', __FILE__, __LINE__);

$row = dbTableFetch('postgresql', array('rsn' => $primaryValue), 1);

if (!$row)
    krynTestSetExit('Can not find row.', __FILE__, __LINE__);

if ($row['numberfloat'] != 34.45)
    krynTestSetExit('Numberfloat is not a float value.', __FILE__, __LINE__);

krynTestetsDeinstallExtension('ts_postgresql');

exit(0); //anything is good

?>