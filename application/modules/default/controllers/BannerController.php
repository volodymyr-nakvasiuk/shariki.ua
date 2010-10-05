<?php
class BannerController extends Zend_Controller_Action {

	public function indexAction(){
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$width = $this->_request->getParam('width');
		$height = $this->_request->getParam('height');
		$img = $this->_request->getParam('img');
		$lnk = $this->_request->getParam('lnk');
		$banner = new Tools_Banner(HOST_NAME,HOST_NAME,$width,$height, false, false, $img, $lnk);
		$code = $banner->getBanner();
		echo $code;
		return;
	}
	
	public function c8Action(){ 
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$width = $this->_request->getParam('width');
		$height = $this->_request->getParam('height');
		$c8_sa = $this->_request->getParam('c8_sa');
		$c8_pid = $this->_request->getParam('c8_pid');
		$banner = new Tools_C8Banner('',$width,$height);
		$code = $banner->getBanner($c8_sa, $c8_pid);
		echo $code;
		return;
	}
	
	public function admixerAction(){ 
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$width = $this->_request->getParam('width');
		$height = $this->_request->getParam('height');
		$banner = new Tools_AdmixerBanner('', $width, $height);
		$code = $banner->getBanner();
		echo $code;
		return;
	}
	
	public function zapchastAction(){ 
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$width = $this->_request->getParam('width');
		$height = $this->_request->getParam('height');
		$banner = new Tools_ZapchastBanner('',$width,$height);
		$code = $banner->getBanner();
		echo $code;
		return;
	}
	
	public function googleAction(){ 
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$width = $this->_request->getParam('width');
		$height = $this->_request->getParam('height');
		$adsId = $this->_request->getParam('adsid');
		$banner = new Tools_GoogleBanner('',$width,$height,$adsId);
		$code = $banner->getBanner();
		echo $code;
		return;
	}
	
	public function yandexAction(){ 
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$width = $this->_request->getParam('width');
		$height = $this->_request->getParam('height');
		$adsId = $this->_request->getParam('adsid', false);
		$channel = $this->_request->getParam('channel', false);
		$fbanner = $this->_request->getParam('fbanner', false);
		$banner = new Tools_YandexBanner('',$width,$height,$fbanner,$adsId,$channel);
		$code = $banner->getBanner();
		echo $code;
		return;
	}
	
	public function redtramAction(){ 
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$banner = new Tools_RedtramBanner('', 0, 0);
		$code = $banner->getBanner();
		echo $code;
		return;
	}
	
	public function marketgidAction(){ 
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
		$width = $this->_request->getParam('width');
		$height = $this->_request->getParam('height');
		$banner = new Tools_MarketgidBanner('', $width, $height);
		$code = $banner->getBanner();
		echo $code;
		return;
	}
}