<?php

class Initialization_BackupController extends Zend_Controller_Action
{

    public function init ()
    {}

    public function indexAction ()
    {
        $this->view->page = "System Backup";

        if ($this->_request->isPost()) {}
    }

    public function createAction ()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->view->page = "System Backup - Create a backup";

        $filename = BACKUP_PATH . "/backup.tar.gz";

        if (file_exists($filename)) {
            unlink($filename);
        }

        exec(
                'APPLICATION_ENV=' . APPLICATION_ENV . ' php ' . APPLICATION_PATH .
                         '/../bin/App/createBackup.php');

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false); // required for certain
                                                 // browsers
        header("Content-Type: application/x-gzip");
        header('Content-Disposition: attachment; filename="backup.tar.gz";');
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($filename));
        readfile($filename);

        unlink($filename);
    }

    public function restoreAction ()
    {
        $this->view->page = "System Backup - Restore from an existing backup";
        $form = new \App\Form\Configuration\Backup\Restore();
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getParams())) {
                $oldname =\pathinfo($form->file->getFileName());

                $newname = BACKUP_PATH . DIRECTORY_SEPARATOR . "backup.tar.gz";

                $form->file->addFilter('Rename',
                        array(
                                'target' => $newname,
                                'overwrite' => true
                        ));

                $form->getValues();

                $form->file->getTransferAdapter()->setOptions(
                        array(
                                'useByteString' => false
                        ));

                $this->view->output = exec(
                        'APPLICATION_ENV=' . APPLICATION_ENV . " php " .
                                 APPLICATION_PATH .
                                 "/../bin/App/restoreBackup.php");
            } else {}
        }
        $this->view->form = $form;
    }
}

