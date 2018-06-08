<?php
namespace controllers;
 use Ubiquity\utils\base\UFileSystem;
use Ubiquity\controllers\Startup;
use Ubiquity\cache\CacheManager;

 /**
 * Controller Main
 * @property \Ajax\JsUtils $jquery
 **/
class Main extends ControllerBase{

	public function index(){		
		$semantic=$this->jquery->semantic();
		$header=$semantic->htmlHeader("header",1);
		$header->asTitle("Welcome to Ubiquity","Version ".\Ubiquity\core\Framework::version);
		$bt=$semantic->htmlButton("btTest","Semantic-UI Button");
		$bt->onClick("$('#test').html('It works with Semantic-UI too !');");
		$menu=\Ubiquity\core\postinstall\Display::semanticMenu("menu",$semantic);
		$item=$menu->addItem("Reset all modifications");
		$item->addIcon("exclamation triangle red");
		$item->addClass("red active");
		$item->getOnClick("Main/clear","#header",["jqueryDone"=>"after","hasLoader"=>false,"ajaxTransition"=>"random"]);
		$this->jquery->compile($this->view);
		$this->loadView("@framework/index/semantic.html");
	}
	
	public function clear(){
		$config=Startup::getConfig();
		UFileSystem::deleteAllFilesFromFolder(Startup::getModelsCompletePath());
		ob_start();
		CacheManager::clearCache($config);
		ob_end_clean();
		UFileSystem::delTree(ROOT.DS."views");
		UFileSystem::delTree(ROOT.DS."controllers");
		UFileSystem::xcopy(ROOT.DS.".save", ROOT);
		$msgId=uniqid("tmp");
		$msg=$this->jquery->semantic()->htmlMessage($msgId,"Remise à zéro réalisée.&nbsp;<div><div id='bt-start-all' class='ui action input'><input type='number' id='counter' value='360'><button id='bt-start' class='ui button'>Démarrer l'épreuve...</button></div>&nbsp;<span style='font-size:120px;font-weight:bold;display:none;' id='timer'></span></div>","icon success");
		$msg->addIcon("check circle");
		$this->jquery->exec("var startTimer=function(duration, display) {var timer = duration, minutes, seconds;
											display.show();
    										var interval=setInterval(function () {
        										minutes = parseInt(timer / 60, 10);seconds = parseInt(timer % 60, 10);
										        minutes = minutes < 10 ? '0' + minutes : minutes;
        										seconds = seconds < 10 ? '0' + seconds : seconds;
										        display.html(minutes + ':' + seconds);
										        if (--timer < 0) {clearInterval(interval);$('#{$msgId}').removeClass('success').addClass('error');}
    										}, 1000);
										}",true);
		$this->jquery->execOn("click","#bt-start","$('#bt-start-all').hide();startTimer($('#counter').val(),$('#timer'));");
		echo $msg;
		echo $this->jquery->compile($this->view);
	}
}
