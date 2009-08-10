<?php
require_once 'pear-database-package.php';

/**
 * The bug system home page
 *
 * This source file is subject to version 3.0 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is
 * available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.
 * If you did not receive a copy of the PHP license and are unable to
 * obtain it through the world-wide-web, please send a note to
 * license@php.net so we can mail you a copy immediately.
 *
 * @category  pearweb
 * @package   Bugs
 * @copyright Copyright (c) 1997-2005 The PHP Group
 * @license   http://www.php.net/license/3_0.txt  PHP License
 * @version   $Id$
 */

response_header('Bugs');

$packages = package::listAllNames();
?>

<h1>PEAR Bug Tracking System</h1>
<style type="text/css">
.bug-box {
    display: table-cell;
    width: 33%;
    padding: 1.0em;
    border:1px solid white;
}

.bug-box.help {
    background-color:#FFFFC8;
    border:1px solid #FF9B64;
}

.bug-box form {
    margin: 2.0em;
}
</style>
<div class="bug-box">
    <h2>Report new Bug</h2>
    <p>Got a test case?</p>

    <p>Reproducable steps?</p>
    <?php if (!empty($packages)) { ?>
        <form method="get" action="/bugs/report.php">
            <select name="package">
                <option selected="selected">Choose your package</option>
                <?php foreach ($packages as $id => $package) { ?>
                    <option name="<?php print $id; ?>"><?php print $package; ?></option>
                <?php } ?>
            </select>
            <input type="submit" name="action" value="Go" />
        </form>
    <?php } else { ?>
        <p>0 packages found.</p>
    <?php } ?>

    <p style="font-size: 0.8em; text-align: right">Psst! Check the tips <?php echo make_link('http://bugs.php.net/how-to-report.php',
                  'on getting your bugs fixed quickly', 'top'); ?>!</p>
</div>


<div class="bug-box">
    <h2>Enhancements</h2>
    <p>Got a patch?</p>
    <p>Or a great use case?</p>
    <?php if (!empty($packages)) { ?>
        <form method="get" action="/bugs/report.php">
            <select name="package">
                <option selected="selected">Choose your package</option>
                <?php foreach ($packages as $id => $package) { ?>
                    <option name="<?php print $id; ?>"><?php print $package; ?></option>
                <?php } ?>
            </select>
            <input type="submit" name="action" value="Go" />
        </form>
    <?php } else { ?>
        <p>0 packages found.</p>
    <?php } ?>
</div>

<div class="bug-box help">
    <h2>Get help</h2>
    <p>Not sure if its a bug?</p>

    <p>Try some of our support channels, and don't forget to <a href="http://pastebin.com">use pastebin</a>.
        <ul>
            <li>The <a href="/support/lists.php">pear-general mailing list</a></li>
            <li>The <a href="irc://efnet/#pear">#pear IRC channel</a> on EFnet</li>
        </ul>
    </p>
    
</div>
<h2 style="margin-top: 2.0em">Search, Tips, Tools and Statistics</h2>
<p>Not what you wanted?</p>
<!-- Shh -->
<ul style="-moz-column-count:2">
    <li><?php echo make_link('search.php', 'Search'); ?> existing bugs.</li>
    <li>See <?php echo make_link('stats.php', 'Bug Statistics'); ?></li>
    <li>Report a <?php print make_bug_link('pearweb', 'report', 'website'); ?> problem.</li>
    <li>Report a <?php print make_bug_link('Documentation', 'report', 'documentation'); ?> problem.</li>
    <li>Check out the <?php echo make_link('/manual/', 'manual'); ?>.</li>
    <li>Check the <?php echo make_link('http://www.nabble.com/Pear---General-f166.html', 'pear-general', 'top'); ?> and <?php echo make_link('http://www.nabble.com/Pear---Dev-f167.html', 'pear-dev', 'top'); ?> mailing list archives.</li>
</ul>

<?php
response_footer();
?>