<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * @author  Arie
 */

class Malam_Attachment
{
    /**
     * @return Attachment
     */
    public function factory()
    {
        return new Attachment();
    }

    public function __construct()
    {
        require_once Kohana::find_file('vendor',
                'php-mime-mail-parser/MimeMailParser.class');
    }

    public function run()
    {
        $config   = Kohana::$config->load('attachment');
         /* @var $config Config_Group */

        /**
         * Credential
         */
        $hostname = Arr::get($config->imap, 'hostname');
        $username = Arr::get($config->imap, 'username');
        $password = Arr::get($config->imap, 'password');

        /**
         * Folder
         */
        $cfolder  = Arr::get($config->folder, 'CFOLDER' );
        $ffolder  = Arr::get($config->folder, 'FFOLDER' );
        $efolder  = Arr::get($config->folder, 'EFOLDER' );
        $excludes = Arr::get($config->folder, 'EXCLUDES');
        $save_to  = Arr::get($config->image,  'save_to' );
        $ds       = DIRECTORY_SEPARATOR;

        /**
         * Try to logged in and get all mail
         */
        $inbox  = imap_open($hostname.$cfolder, $username, $password);
        $emails = imap_sort($inbox, SORTARRIVAL, 1);

        if (! $emails)
            return;

        $max    = Arr::get($config->imap, 'maxfiles', 100);
        $emails = array_slice($emails, 0, $max);

        foreach ($emails as $i => $numb)
        {
            try {

                $mfolder = $efolder;

                $headers = imap_fetchheader($inbox, $numb, FT_PREFETCHTEXT);
                $body    = imap_body($inbox, $numb);
                $data    = $headers."\n".$body;

                $parser = new MimeMailParser();
                $parser->setText($data);

                $from   = $parser->getHeader('from');

                if (! preg_match("#{$excludes}#i", $from))
                {
                    $subject = preg_replace('/[^a-z0-9-_]/i', '-', $parser->getHeader('subject'));
                    $subject = preg_replace('/-{1,}/', ' ', $subject);

                    foreach ($parser->getAttachments() as $attachment)
                    {
                        /* @var $attachment MimeMailParser_attachment */

                        $file = $save_to . $ds . $attachment->getFilename();
                        file_put_contents($file, $attachment->getContent());
                    }

                    $mfolder = $ffolder;
                    unset($data);
                }

                /**
                 * http://www.electrictoolbox.com/delete-message-php-imap-gmail/
                 */
                if (preg_match('#(gmail|google)#i', $hostname))
                {
                    imap_mail_move($inbox, "$numb:$numb", $mfolder);
                    imap_delete($inbox, "$numb:$numb");
                }

                imap_mail_move($inbox, $numb, $mfolder);
                imap_delete($inbox, $numb);
            }
            catch (ErrorException $e)
            {
                Kohana::$log->add(Log::DEBUG, "Catch Error:\n\t:error\nTrace: :trace", array(
                    ':error'    => $e->getMessage(),
                    ':trace'    => $e->getTraceAsString()
                ))->write();
            }
        }

        imap_expunge($inbox);
        imap_close($inbox);
    }
}