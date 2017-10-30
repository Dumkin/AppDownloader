<?php
namespace app\forms;

use php\gui\framework\AbstractForm;
use php\gui\event\UXMouseEvent; 
use php\gui\event\UXEvent; 
use php\lang\System;
use php\gui\event\UXKeyEvent; 

class MainForm extends AbstractForm
{
    /**
     * @event panelStart_Start.action 
     */
    function App_loadForm(UXEvent $event = null)
    {
        $this->loadForm('Install', false, true);
    }
    /**
     * @event panelStart_Close.click 
     */
    function App_Close(UXMouseEvent $event = null)
    {    
        app()->shutdown();
    }
    /**
     * @event keyDown-F1 
     */
    function Info(UXKeyEvent $event = null)
    {    
        alert("Разработчик: DumkinDV");
    }
    /**
     * @event panelStart_Settings.click 
     */
    function doPanelStart_SettingsClick(UXMouseEvent $event = null)
    {    
        $this->loadForm('Settings', false, true);
    }
}