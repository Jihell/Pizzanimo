<?php
/**
 * ========================== MINISITE CONFIGURATION ===========================
 * 
 * Page direct path by menu (add the website address) :
 * /Home		: Home page
 */

/**
 * Set the debug mode
 */
define('DEBUG', false);

/**
 * Lang of the website, list of localisations files in /lang
 */
define('DEFAULT_LANG', 'FR');

/**
 * Comment this line to hide the debug callstack
 */
\He\Trace::init();

/**
 * Enter the name of the table where you have to stor the contacts retrieve by
 * the contact form.
 * 
 * REMEMBER !!! NO SPACES OR SPECIAL CHARS !!!
 */
define('DATABASE_TABLE', 'highscore');

/**
 * Set the sender email, no need for change here
 */
define('EMAIL_FROM', 'no-reply@'.\He\Param::getServerAddress());

/**
 * Set the name of the mail's sender
 */
define('EMAIL_SENDER_NAME', 'PJC - joseph-lemoine.fr');

/**
 * Mail title
 */
define('EMAIL_TITLE', '{%MAIL_TITLE} '.$_SERVER['SERVER_NAME']);
