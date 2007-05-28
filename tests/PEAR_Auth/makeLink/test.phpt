--TEST--
PEAR_Auth->makeLink()
--FILE--
<?php
// setup
require dirname(dirname(__FILE__)) . '/setup.php.inc';
try {
    $auth_user->makeLink();
    $phpunit->assertFalse(true, 'Exception not thrown!');
} catch (Exception $e) {
    $phpunit->assertEquals('Programmer error: please report to pear-dev@lists.php.net.' .
                ' $auth_user not initialized with data()', $e->getMessage(), 'exception');
}

$auth_user->handle = 'cellog';
$auth_user->name = 'Greg "the yellow dart" Beaver';
$phpunit->assertEquals('<a href="/user/cellog/">Greg &quot;the yellow dart&quot; Beaver</a>',
    $auth_user->makeLink(), 'test');
?>
===DONE===
--EXPECT--
===DONE===