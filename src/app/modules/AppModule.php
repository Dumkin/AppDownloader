<?php
namespace app\modules;

use php\gui\framework\AbstractModule;
use php\gui\framework\ScriptEvent;
use php\io\File;
use php\lib\fs;
use php\gui\UXDialog; 
use Exception;
use php\io\Stream;
use php\format\JsonProcessor;

class AppModule extends AbstractModule
{
    /**
     * @event action 
     */
    function doAction(ScriptEvent $event = null)
    {
        // Для того чтобы foreach в MainForm не ругался на не существующий массив
        $GLOBALS['List']['URL'] = [];
        
        try {
           $parser = new JsonProcessor(JsonProcessor::DESERIALIZE_AS_ARRAYS);
           $data = Stream::getContents('database.json');
           $json = $parser->parse($data);
           for ($i = 0; $i < $json['count']; $i++) {
                $GLOBALS['Apps'][$i]['Name'] = $json[$i . "_Name"];
                $GLOBALS['Apps'][$i]['Official'] = $json[$i . "_Official"];
                $GLOBALS['Apps'][$i]['Logo'] = $json[$i . "_Logo"];
                $GLOBALS['Apps'][$i]['Description'] = $json[$i . "_Description"];
                $GLOBALS['Apps'][$i]['URL'] = $json[$i . "_Install"];
           }
        } catch (Exception $e) {
           UXDialog::showAndWait('Произошла ошибка при чтении базы данных!', 'ERROR');
        }
        $TempFolder = fs::abs('./') . "/Temp";
        if (fs::isDir($TempFolder)) {
            fs::clean($TempFolder);
        }
        else 
        {
            $dir = new File($TempFolder);

            if ($dir->mkdirs()) {
                
            } else {
                UXDialog::showAndWait('Произошла ошибка при создании папки!', 'ERROR');
            }
        }
    }
}