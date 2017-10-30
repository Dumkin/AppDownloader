<?php
namespace app\forms;

use php\gui\framework\AbstractForm;
use php\gui\event\UXMouseEvent; 
use php\gui\event\UXEvent; 
use php\gui\event\UXWindowEvent; 

class Settings extends AbstractForm
{
    /**
     * @event Exit.click 
     */
    function doExitClick(UXMouseEvent $event = null)
    {    
        app()->shutdown();
    }
    /**
     * @event Back.click 
     */
    function doBackClick(UXMouseEvent $event = null)
    {    
        $this->loadForm('MainForm', false, true);
    }
    /**
     * @event pagination.action 
     */
    function doPaginationAction(UXEvent $event = null)
    {    
        $this->label->text = 'Количество потоков для скачивания (Рекомендуется: 4-5): ' . ($this->pagination->selectedPage + 1);
        $GLOBALS['stream'] = $this->pagination->selectedPage + 1;
        $this->ini->set('stream', ($this->pagination->selectedPage + 1));
    }
    /**
     * @event showing 
     */
    function doShowing(UXWindowEvent $event = null)
    {    
        $this->pagination->selectedPage = $this->ini->get('stream') - 1;
    }
}