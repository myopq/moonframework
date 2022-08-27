<?php

declare(strict_types=1);

namespace App\Http;

use Moon\Facades\C;
use Moon\HttpController;
use Moon\HttpRequest;

class TranslateController extends HttpController
{
    public function index(HttpRequest $httpRequest)
    {
        $tips = "";
        $langs = $this->list_directory_content(resource_path('lang'));

        foreach ($langs as &$lang) {
            $lang['files'] = $this->list_directory_content($lang['path']);
        }
        unset($lang);
        $this->assign("langs", $langs);

        $filenameFrom = $_POST['filename_from'] ?? '';
        $this->assign("filename_from", $filenameFrom);
        $langTo = $_POST['lang_to'] ?? '';
        $this->assign("lang_to", $langTo);

        if ($filenameFrom) {
            $fileContent = file_get_contents($filenameFrom);
            $langVars = require $filenameFrom;
            $this->assign("fileContent", $fileContent);
            $this->assign("langVars", $langVars);

            if (!empty($_POST['Review'])) {
                $newContentStr = $_POST['newContent'];
                $newContent = explode("\n", $_POST['newContent']);
                $this->assign('newContentStr', $newContentStr);
                if (count($newContent) != count($langVars)) {
                    exit('count no match.');
                }

                $content = "<?php\nreturn[\n";
                $pos = 0;
                foreach ($langVars as $key => $value) {
                    $content .= "    '{$key}' => '" . str_replace("'", "\\'" , trim($newContent[$pos])) . "', \n";
                    $pos++;
                }
                $content .= "\n];";
                $this->assign("result", $content);
            }

            if (!empty($_POST['Save'])) {
                $saveTo = $langTo . '/' . pathinfo($filenameFrom)['basename'];
                file_put_contents($saveTo, $_POST['areaReview']);
                $tips = "saved: " . $saveTo;
            }
        }

        
        $this->assign("tips", $tips);
        $this->display();
    }

    private function list_directory_content($dir)
    {
        $files = [];
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (($file = readdir($handle)) !== false) {
                    if ($file == '.' || $file == '..') continue;

                    if (is_dir($file)) {
                        $files[] = ['type' => 'dir', 'file' => $file, 'path' => $dir . '/' . $file];
                    } else {
                        $files[] = ['type' => 'file', 'file' => $file,  'path' => $dir . '/' . $file];
                    }
                }
                closedir($handle);
            }
        }

        usort($files, function($a, $b) {
            return strcmp($a['file'], $b['file']);
        });
        return $files;
    }
}
