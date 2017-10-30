<?php
namespace app\forms;

use Exception;
use php\gui\framework\AbstractForm;
use php\gui\event\UXEvent; 
use php\gui\UXDialog; 


class Message extends AbstractForm
{

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $event = null)
    {
        //$this->free();
        // Generated
        app()->hideForm('Message');
        
        // Generated
        UXDialog::showAndWait('', 'ERROR');
    }

}
