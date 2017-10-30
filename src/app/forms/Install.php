<?php
namespace app\forms;

use php\lib\reflect;
use php\gui\framework\AbstractForm;
use php\gui\event\UXEvent; 
use php\gui\event\UXMouseEvent; 
use php\gui\event\UXKeyEvent; 
use php\gui\event\UXWindowEvent; 
use php\gui\UXDialog;

class Install extends AbstractForm
{
    /**
     * @event App_Exit.click 
     */
    function Exit(UXMouseEvent $event = null)
    {
        app()->shutdown();
    }
    /**
     * @event Official.action 
     */
    function Official(UXEvent $event = null)
    {
        browse($GLOBALS['Apps'][$this->Table->selectedIndex]['Official']);
    }
    /**
     * @event keyDown-F1 
     */
    function F1(UXKeyEvent $event = null)
    {    
        alert("Разработчик: DumkinDV");
    }
    /**
     * @event DownloadForce.action 
     */
    function DownloadForce(UXEvent $event = null)
    {
        if ($this->dirChooser->execute()) { 
            $this->showPreloader('Начало загрузки');

            $this->ForceDownloader->destDirectory = $this->dirChooser->file;  
            $this->ForceDownloader->urls = $GLOBALS['Apps'][$this->Table->selectedIndex]['URL'];
            $this->ForceDownloader->start();
        }
    }
    /**
     * @event App_Downloads.click 
     */
    function DownloadList(UXMouseEvent $event = null)
    {
        if ($GLOBALS['List']['Count'] == 0) {
            UXDialog::showAndWait("Вы не выбрали ни одного приложения!");
            return;
        }
        if (UXDialog::confirm('Вы уверены, что хотите начать скачивание?')) {
            if ($this->dirChooser->execute()) {  
                $this->showPreloader('Начало загрузки');
                $this->Downloader->destDirectory = $this->dirChooser->file;  
                $this->Downloader->urls = $GLOBALS['List']['URL'];
                $this->Downloader->threadCount = $this->ini->get('stream');
                $this->Downloader->start();
            }
        }
    }
    /**
     * @event Table.click 
     */
    function Table(UXMouseEvent $event = null)
    {    
        $this->Official->visible = $this->DownloadForce->visible = $this->ListDownload->visible = true;
        $this->Logo->url = $GLOBALS['Apps'][$this->Table->selectedIndex]['Logo'];
        $this->Description->text = $GLOBALS['Apps'][$this->Table->selectedIndex]['Description'];
        $this->ListDownload->text = 'Добавить в список загрузок';
        foreach ($GLOBALS['List']['URL'] as $value) {
            if ($GLOBALS['Apps'][$this->Table->selectedIndex]['URL'] == $value) {
                $this->ListDownload->text = 'Убрать из списка загрузок';
                return;
            }
        }
    }
    /**
     * @event ListDownload.action 
     */
    function ClickListDownload(UXEvent $event = null)
    {    
        if ($this->ListDownload->text == 'Добавить в список загрузок') {
            $this->ListDownload->text = 'Убрать из списка загрузок';
            
            $GLOBALS['List']['Count'] = $GLOBALS['List']['Count'] + 1;
            $this->CountDownload->text = $GLOBALS['List']['Count'];
            
            $GLOBALS['List']['URL'][] = $GLOBALS['Apps'][$this->Table->selectedIndex]['URL'];
    
            $this->Table->items->insert($this->Table->selectedIndex, ['Name' => 'Temp']);
            $this->Table->items->removeByIndex($this->Table->selectedIndex);
            $this->Table->items->insert($this->Table->selectedIndex, ['Name' => '⬇ ' . $GLOBALS['Apps'][$this->Table->selectedIndex]['Name']]);
            $this->Table->items->removeByIndex($this->Table->selectedIndex);
        } else {
            $this->ListDownload->text = 'Добавить в список загрузок';
            
            $GLOBALS['List']['Count'] = $GLOBALS['List']['Count'] - 1;
            $this->CountDownload->text = $GLOBALS['List']['Count'];

            foreach ($GLOBALS['List']['URL'] as $key => $value) {
                if ($GLOBALS['Apps'][$this->Table->selectedIndex]['URL'] == $value) {
                    unset($GLOBALS['List']['URL'][$key]);
                }
            }
            $tempArray = [];
            foreach ($GLOBALS['List']['URL'] as $key => $value) {
                $tempArray[] = $value;
            }
            $GLOBALS['List']['URL'] = $tempArray;
            
            $this->Table->items->insert($this->Table->selectedIndex, ['Name' => 'Temp']);
            $this->Table->items->removeByIndex($this->Table->selectedIndex);
            $this->Table->items->insert($this->Table->selectedIndex, ['Name' => $GLOBALS['Apps'][$this->Table->selectedIndex]['Name']]);
            $this->Table->items->removeByIndex($this->Table->selectedIndex);
        }
    }
    /**
     * @event showing 
     */
    function doShowing(UXWindowEvent $event = null)
    {    
        $this->Load_Table->call();
    }
}