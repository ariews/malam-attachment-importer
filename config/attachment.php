<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  Arie
 */

return array(
    'image'         => array(
        /**
         * Will save all attachment to this folder
         * !!Make sur this folder is exists and under DOCROOT
         */
        'save_to'   => 'uploads',
        /**
         * only save attachments with this extensions
         */
        'exts'      => array('jpg', 'jpeg', 'png', 'gif'),
    ),

    /**
     * IMAP information
     */
    'imap'          => array(
        'hostname'  => '{hostname:port/imap/ssl}',
        'password'  => 'password',
        'username'  => 'username',
        /**
         * max mail to be checked
         */
        'maxfiles'  => '100',
    ),

    'folder'        => array(
        /**
         * Only check this folder
         */
        'CFOLDER'   => 'Inbox',
        /**
         * Move all mail to this folder after checked
         * !!Make sure this folder exists
         */
        'FFOLDER'   => 'Downloaded',
        /**
         * Never check mail from this sender
         * !!NULL or REGEX ONLY
         */
        'EXCLUDES'  => NULL,
        /**
         * Move All excludes to this folder
         * !!Make sure this folder exists
         */
        'EFOLDER'   => 'Excludes',
    )
);