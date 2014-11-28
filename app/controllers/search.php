<?php namespace controllers;
use core\view as View;
use \models\Search as MSearch;
use \Exception;

class Search extends \core\controller {
  
  public function get(){
    
    
    $data['dosearch'] = MSearch::searchQueriesExist();
    
    if ($data['dosearch']){
      try {
        switch ($_GET['lookfor']) {
          case 'u' :
            $data['res'] = MSearch::userSearch();
            break;
          case 'qa':
            $data['res'] = MSearch::QASearch();
            break;
          default:
            throw new Exception('Invalid search terms');
        }
      } catch (Exception $e) {
        $this->handleException($e);
        echo $e->getMessage(); //TODO change
        $data['dosearch'] = false;
      }
    } else echo 'foo';
    
    
    $data['title'] = 'Search';
    $data['styles'] = ['search.css'];
    $data['scripts'] = ['jquery' => 1, 'jquery.age' => 1, 'jquery.address' => 1, 'semantic' => 1];
    $data['scripts']['custom'] = ['search.js'];
    
    $data['activeQA'] = (isset($_GET['lookfor']) and $_GET['lookfor'] === 'qa');
    
    View::rendertemplate('header', $data);
    View::render('search', $data);
    View::rendertemplate('footer', $data);
    
  }
}
?>