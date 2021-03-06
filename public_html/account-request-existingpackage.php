<?php
/*
   +----------------------------------------------------------------------+
   | PEAR Web site version 1.0                                            |
   +----------------------------------------------------------------------+
   | Copyright (c) 2001-2005 The PHP Group                                |
   +----------------------------------------------------------------------+
   | This source file is subject to version 2.02 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available at through the world-wide-web at                           |
   | http://www.php.net/license/2_02.txt.                                 |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Authors:                                                             |
   +----------------------------------------------------------------------+
   $Id$
*/

redirect_to_https();
require_once 'HTML/QuickForm2.php';
require_once 'HTML/QuickForm2/Renderer/PEAR.php';
/** @todo Remove once in QF2 */
require_once 'HTML/QuickForm2/Element/InputNumber.php';
require_once 'HTML/QuickForm2/Element/InputEmail.php';
require_once 'HTML/QuickForm2/Element/InputUrl.php';
require_once 'Damblan/Mailer.php';
require_once 'Text/CAPTCHA/Numeral.php';
require_once 'services/HoneyPot.php';

$numeralCaptcha = new Text_CAPTCHA_Numeral();
session_start();

$display_form = true;
$width        = 60;
$errors       = array();
$jumpto       = 'handle';

$stripped = @array_map('strip_tags', $_POST);

response_header('Request Account');

echo '<h1>Request Account</h1>';

do {
    if (isset($stripped['submit'])) {

        if (empty($stripped['handle'])
            || !ereg('^[0-9a-z_]{2,20}$', $stripped['handle']))
        {
            $errors[] = 'Username is invalid.';
            $display_form = true;
        }

        if (empty($_POST['read_everything']['comments_read'])) {
            $errors[] = 'Obviously you did not read all the comments'
                      . ' concerning the need for an account. Please read '
                      . 'them again.';
            $display_form = true;
        }

        if (isset($_POST['purposecheck']) && count($_POST['purposecheck'])) {
            $errors[] = 'The purpose(s) you selected do not require a PEAR account.';
            $display_form = true;
        }

        if (!isset($stripped['captcha']) || !isset($_SESSION['answer'])
            || $stripped['captcha'] != $_SESSION['answer']
        ) {
            $errors[] = 'Incorrect CAPTCHA';
            $display_form = true;
        }

        $p = isset($stripped['existingpackage']) ? $stripped['existingpackage'] : '';
        $package = $dbh->getOne('SELECT count(id) FROM packages WHERE packages.name = ?',
              array($p));
        if (!$package) {
            $errors[] = 'Package "' .
                htmlspecialchars($p) . '" does not ' .
                'exist, please choose a pre-existing package name';
        }

        if ($errors) {
            break;
        }

        //  The add method performs further validation then creates the account
        include_once 'pear-database-user.php';
        $ok = user::add($stripped);
        if (PEAR::isError($ok)) {
            $errors[] = 'This email address has already been registered by another user';
            $display_form = true;
            break;
        }

        if (isset($stripped['display_form'])) {
            $display_form = $stripped['display_form'];
        }

        if (is_array($ok)) {
            $errors = $ok;
            break;
        } elseif ($ok === true) {
            report_success('Your account request has been submitted, it will'
                  . ' be reviewed by a human shortly.  This may take from'
                  . ' two minutes to several days, depending on how much'
                  . ' time people have.'
                  . ' You will get an email when your account is open,'
                  . ' or if your request was rejected for some reason.');

            $mailData = array(
                'username'  => $stripped['handle'],
                'firstname' => $stripped['firstname'],
                'lastname'  => $stripped['lastname'],
                'package'   => $stripped['existingpackage'],
            );

            if (!DEVBOX) {
                $mailer = Damblan_Mailer::create('pearweb_account_request', $mailData);
                $additionalHeaders['To'] = PEAR_GROUP_EMAIL;
                $mailer->send($additionalHeaders);
            }
        } elseif ($ok === false) {
            $msg = 'Your account request has been submitted, but there'
                 . ' were problems mailing one or more administrators.'
                 . ' If you don\'t hear anything about your account in'
                 . ' a few days, please drop a mail about it to the'
                 . ' <i>pear-dev</i> mailing list.';
            report_error($msg, 'warnings', 'WARNING:');
        }

        $display_form = false;
    }
} while (0);

try {
    $sHelper = new Pearweb_Service_HoneyPot(HONEYPOT_API_KEY);
    $ip      = $_SERVER['REMOTE_ADDR'];
    $sHelper->check($ip);

} catch (Exception $e) {
    report_error($e);
    $display_form = false;
}

if ($display_form) {
$mailto = '<a href="mailto:' . PEAR_DEV_EMAIL . '">PEAR developers mailing list</a>';
    echo <<<MSG
<h1>PLEASE READ THIS BEFORE SUBMITTING!</h1>
<p>
 You have chosen to request an account for helping to develop an existing package.
</p>
<p>
 <strong>Before</strong> filling out this form, you must seek approval by mailing
  the $mailto and developers of the package.  If you already have a PEAR account,
  all you need to do is to request SVN karma (again, ask for help on the $mailto).
  You do not need a new account for each package that you maintain.
</p>
<h3>
 You do NOT need an account to use PEAR, download PEAR packages, to submit bugs/patches
 or to suggest new features.  If you wish to submit bugs/patches or suggest new features,
 use the <a href="http://pear.php.net/bugs">PEAR bug tracker</a> for the package you wish
 to help develop.
</h3>
<p>
Please use the &quot;latin counterparts&quot; of non-latin characters (for instance th instead of &thorn;).
</p>

MSG;

    print '<a name="requestform" id="requestform"></a>';

    report_error($errors);

    $form = new HTML_QuickForm2('account-request-existingpackage', 'post', array('action' => 'account-request-existingpackage.php#requestform'));
    $form->removeAttribute('name');

    $renderer = new HTML_QuickForm2_Renderer_PEAR();

    $hsc = array_map('htmlspecialchars', $stripped);
    // Set defaults for the form elements
    $form->addDataSource(new HTML_QuickForm2_DataSource_Array(array(
        'handle'        => @$hsc['handle'],
        'firstname'     => @$hsc['firstname'],
        'lastname'      => @$hsc['lastname'],
        'email'         => @$hsc['email'],
        'showemail'     => @$hsc['showemail'],
        'existingpackage'    => @$hsc['existingpackage'],
        'purpose'       => @$hsc['purpose'],
        'homepage'      => @$hsc['homepage'],
        'moreinfo'      => @$hsc['moreinfo'],
        'read_everything' => @$hsc['read_everything'],
    )));

    $form->addElement('text', 'handle', array('placeholder' => 'psmith', 'maxlength' => "20", 'accesskey' => "r", 'required' => 'required'))->setLabel('Use<span class="accesskey">r</span>name:');
    $form->addElement('text', 'firstname', array('placeholder' => 'Peter', 'required' => 'required'))->setLabel('First Name:');
    $form->addElement('text', 'lastname', array('placeholder' => 'Smith', 'required' => 'required'))->setLabel('Last Name:');
    $form->addElement('password', 'password', array('size' => 10, 'required' => 'required'))->setLabel('Password:');
    $form->addElement('password', 'password2', array('size' => 10, 'required' => 'required'))->setLabel('Repeat Password:');
    $form->addElement('number', 'captcha', array('maxlength' => 4, 'required' => 'required'))->setLabel("What is " . $numeralCaptcha->getOperation() . '?');
    $_SESSION['answer'] = $numeralCaptcha->getAnswer();
    $form->addElement('email', 'email', array('placeholder' => 'you@example.com', 'required' => 'required'))->setLabel('Email Address:');
    $form->addElement('checkbox', 'showemail')->setLabel('Show email address?');
    $form->addElement('text', 'existingpackage', array('placeholder' => 'Category_PackageName', 'required' => 'required'))->setLabel('Package Name:');

    $invalid_purposes = array(
        'Learn about PEAR.',
        'Submit patches/bugs.',
        'Suggest new features.',
        'Download PEAR Packages.',
    );

    $purpose = $form->addGroup('purposecheck')->setLabel('Purpose of your PEAR account:');

    $checkbox = array();
    foreach ($invalid_purposes as $i => $purposeKey) {
        $purpose->addElement('checkbox', $i, array('checked' => !empty($_POST['purposecheck'][$i])? 'checked' : ''))
                ->setLabel($purposeKey);
    }



    $form->addElement('textarea', 'purpose',
            array('cols' => 40, 'rows' => 5, 'required' => 'required', 'placeholder' => 'I will improve...'))->setLabel('How will you help the package?');
    $form->addElement('url', 'homepage', array('placeholder' => 'http://example.com'))->setLabel('Homepage:'
            . '<p class="cell_note">(optional)</p>');
    $form->addElement('textarea', 'moreinfo',
            array('cols' => 40, 'rows' => 5, 'placeholder' => "I am a developer who has ..."))->setLabel('More relevant information about you:'
            . '<p class="cell_note">(optional)</p>');
    $form->addGroup('read_everything')->addElement('checkbox', 'comments_read', array('required' => 'required'))->setLabel('I have read EVERYTHING on this page');
    $form->addElement('submit', 'submit')->setLabel('Submit Request');

    print $form->render($renderer);

}

response_footer();
