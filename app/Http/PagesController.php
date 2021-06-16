<?php

namespace App\Http;

use App\Http\BaseController;

class PagesController extends BaseController
{
    public $pageCategory;
    public $htmlFileRoot;

    public function __construct($pageCategory = '') {
        $this->pageCategory = $pageCategory;
        $this->htmlFileRoot = dirname(dirname(__FILE__)).'/Html';
    }

    public function view() {

        if($this->pageCategory == 'pdf') {
            return self::pdfRender();
        } else if($this->pageCategory = 'excel') {

        }
    }

    public function pdfRender() {

        $pagecontents = file_get_contents($this->htmlFileRoot.'/pdf.html');

        echo $pagecontents;





        return "";
    }
}
