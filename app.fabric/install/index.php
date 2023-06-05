<?php
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);


class app_fabric extends CModule{
	
	function __construct(){
		$arModuleVersion = array();
		include(__DIR__."/version.php");

		$this->MODULE_ID = "app.fabric";
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = Loc::getMessage("APP_FABRIC_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("APP_FABRIC_DESCRIPTION");

		$this->PARTNER_NAME = Loc::getMessage("APP_FABRIC_VENDOR_NAME");
		$this->PARTNER_URI = Loc::getMessage("APP_FABRIC_VENDOR_URI");
	}

	function DoInstall(){
		if($this->isVersionD7()){
			ModuleManager::registerModule($this->MODULE_ID);

			\Bitrix\Main\EventManager::getInstance()->registerEventHandler('main', 'OnProlog', 'app.fabric', '\App\Fabric\Kernel', 'start');

			include __DIR__.'/../lib/install/spl.php';
            \App\Fabric\Install\Spl::beforeInstall();
            \App\Fabric\Install\Spl::load(\App\Fabric\Install\Spl::path()->system()->folder().'/lib/install/*php');
            \App\Fabric\Install\DB::install();
		}
	}

	function DoUninstall(){		
		ModuleManager::unRegisterModule($this->MODULE_ID);

        //(new \Miratorg\Fabric\Install\Master())->uninstall();
	}

	function isVersionD7(){
		return CheckVersion(ModuleManager::getVersion('main'), "14.0.0");
	}
}