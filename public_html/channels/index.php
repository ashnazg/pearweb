<?php
/*
   +----------------------------------------------------------------------+
   | PEAR Web site version 1.0                                            |
   +----------------------------------------------------------------------+
   | Copyright (c) 2005 The PEAR Group                                    |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.0 of the PHP license,       |
   | that is bundled with this package in the file LICENSE, and is        |
   | available at through the world-wide-web at                           |
   | http://www.php.net/license/3_0.txt.                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Authors: Martin Jansen <mj@php.net>                                  |
   +----------------------------------------------------------------------+
   $Id$
*/
require_once 'pear-database-channel.php';

$channels = channel::listActive();
$inactive_channels = array();
if (auth_check('pear.admin')) {
    $inactive_channels = channel::listInactive();
}
response_header("Channels", false, '<link rel="alternate" type="application/xbel+xml" href="xbel.php" title="Channel list as XBEL" />');

$tabs = array("List" => array("url" => "/channels/index.php",
                              "title" => "List Sites."),
              "Add Site" => array("url" => "/channels/add.php",
                                  "title" => "Add your site.")
              );
?>

<h1>Channels</h1>

<?php print_tabbed_navigation($tabs); ?>

<h2>What&#39;s that?</h2>

<p>A number of third-party sites make it possible to install their
software package using the new <a href="/manual/en/guide.migrating.channels.php">channels</a>
feature of PEAR &ge; 1.4.0.  Specific installation instructures are
provided on the individual pages.</p>

<h2>List of Sites</h2>
<p>There are <?php print count($channels); ?> channel(s) we know of.</p>
<ul>
<?php foreach ($channels as $channel) { ?>
  <li>
    <a href="<?php print $channel['project_link']; ?>" title="<?php print $channel['name']; ?>"><?php print $channel['project_label']; ?></a>
    <?php if (auth_check('pear.admin')) { ?>(<a href="edit.php?channel=<?php print $channel['name']; ?>">edit</a>)<?php } ?>
  </li>
<?php } ?>
</ul>

<p><a href="/channels/add.php">Add your site</a>, or <a href="/channels/xbel.php" rel="alternate">grab the bookmarks</a></p>

<?php if (auth_check('pear.admin')) { ?>
    <h2>Sites to be Approved</h2>
    <p>There are <?php print count($inactive_channels); ?> site(s) to be approved.</p>
    <ul>
    <?php foreach ($inactive_channels as $channel) { ?>
      <li>
        <a href="<?php print $channel['project_link']; ?>" title="<?php print $channel['name']; ?>"><?php print $channel['project_label']; ?></a>
    <?php if (auth_check('pear.admin')) { ?>(<a href="edit.php?channel=<?php print $channel['name']; ?>">edit</a>)<?php } ?>      </li>
    <?php } ?>
    </ul>
<?php } ?>

<h2>Channel server software</h2>
<p>Want to host your own channel? </p>
<ul>
    <li><a href="http://pear.chiaraquartet.net">Chiara_PEAR_Server</a> (<a href="http://greg.chiaraquartet.net/archives/123-Setting-up-your-own-PEAR-channel-with-Chiara_PEAR_Server-the-official-way.html">documentation</a>)</li>
    <li><a href="http://svn.php.net/viewvc/pear2/sandbox/SimpleChannelServer/trunk/">SimpleChannelServer</a> (<a href="http://saltybeagle.com/2008/12/using-simplechannelserver-to-manage-a-pear-channel-on-google-code/">documentation</a>)</li>
    <li><a href="http://www.pirum-project.org/">Pirum</a> (<a href="http://blog.stuartherbert.com/php/2011/03/30/setting-up-your-own-pear-channel/">documentation</a>)</li>
    <li><a href="http://pearfarm.org/">Pearfarm</a></li>
</ul>

<?php
response_footer();
?>
