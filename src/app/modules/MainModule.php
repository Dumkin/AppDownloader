<?php
namespace app\modules;

use App;
use php\lib\reflect;
use Exception;
use php\gui\framework\AbstractModule;
use php\gui\framework\ScriptEvent; 
use php\io\Stream;
use php\gui\UXDesktop;
use php\gui\UXDialog;
use php\time\Time;

class MainModule extends AbstractModule
{
    /**
     * @event Load_Table.action 
     */
    function doLoad_TableAction(ScriptEvent $event = null)
    {
        $this->Table->items->clear();
        $this->DownloadForce->visible = $this->ListDownload->visible = $this->Official->visible = false;
        
        foreach ($GLOBALS['Apps'] as $key => $value) {
            $this->Table->items->add(['Name' => $GLOBALS['Apps'][$key]['Name']]);  
        }
    }
    /**
     * @event Downloader.progress 
     */
    function doDownloaderProgress(ScriptEvent $event = null)
    {    
        $this->form('Install')->showPreloader("Загрузка " . (count($this->Downloader->downloadedUrls) + 1) . " из " . $GLOBALS['List']['Count']);
    }
    /**
     * @event Downloader.successAll 
     */
    function doDownloaderSuccessAll(ScriptEvent $event = null)
    {    
        $GLOBALS['List']['Count'] = 0;
        $this->CountDownload->text = $GLOBALS['List']['Count'];
        $GLOBALS['List']['URL'] = [];
        
        $this->Load_Table->call();
        
        $this->form('Install')->hidePreloader();
        
        if (UXDialog::confirm("Загрузка успешно завершена!\nХотите открыть папку?")) {
            $Desktop = new UXDesktop();
            try {
                $Desktop->open($this->dirChooser->file);
            } catch (Exception $Exception) {
                $Now = Time::now();
                Stream::putContents("app.log", '----' . $Now->toString('HH:mm dd.MM.yyyy') . '----' . "\n$Exception\r\n", "a+");
                UXDialog::showAndWait("Не удалось открыть папку!\nКод ошибки сохранён в лог файл!");
            }
        }
    }
    /**
     * @event Downloader.errorOne 
     */
    function doDownloaderErrorOne(ScriptEvent $event = null)
    {    
        if (UXDialog::confirm("Во время загрузки приложений произошла ошибка!\nПовторить попытку?")) {
            $this->showPreloader('Начало загрузки');
                
            $this->Downloader->destDirectory = $this->dirChooser->file;  
            $this->Downloader->urls = $GLOBALS['List']['URL'];
            $this->Downloader->start();
        } else {
            $GLOBALS['List']['Count'] = 0;
            $this->CountDownload->text = $GLOBALS['List']['Count'];
            $GLOBALS['List']['URL'] = [];
            
            $this->Load_Table->call();
            
            $this->form('Install')->hidePreloader();
        }
    }
    /**
     * @event ForceDownloader.progress 
     */
    function doForceDownloaderProgress(ScriptEvent $event = null)
    {
        $GLOBALS['Force']['File'] = $event->file;
        $this->form('Install')->showPreloader('Загрузка ' . round($event->progress / $event->max * 100, 1) . '%');
    }
    /**
     * @event ForceDownloader.successAll 
     */
    function doForceDownloaderSuccessAll(ScriptEvent $event = null)
    {
        $this->form('Install')->hidePreloader();
        
        $Desktop = new UXDesktop();
        try {
            $Desktop->open($GLOBALS['Force']['File']);
        } catch (Exception $Exception) {
            $Now = Time::now();
            Stream::putContents("app.log", '----' . $Now->toString('HH:mm dd.MM.yyyy') . '----' . "\n$Exception\r\n", "a+");
            UXDialog::showAndWait("Не удалось открыть файл!\nКод ошибки сохранён в лог файл!");
        }
    }
    /**
     * @event ForceDownloader.errorOne 
     */
    function doForceDownloaderErrorOne(ScriptEvent $event = null)
    {
        if (UXDialog::confirm("Во время загрузки приложения произошла ошибка!\nПовторить попытку?")) {
            $this->showPreloader('Начало загрузки');
                
            $this->ForceDownloader->destDirectory = $this->dirChooser->file;  
            $this->ForceDownloader->urls = $GLOBALS['Apps'][$this->Table->selectedIndex]['URL'];
            $this->ForceDownloader->start();
        } else {
            $this->form('Install')->hidePreloader();
            
            $GLOBALS['List']['Count'] = 0;
            $this->CountDownload->text = $GLOBALS['List']['Count'];
            $GLOBALS['List']['URL'] = [];
            
            $this->Load_Table->call();
        }
    }
}