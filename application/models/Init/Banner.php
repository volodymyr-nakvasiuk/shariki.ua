<?php
class Init_Banner {
	
	protected $_controller;
	protected $_beMassive = true;
	protected $_width;
	protected $_height;
	protected $_bannersArr = array(
					"Local"=>1,
					"Admixer"=>1,
					"Advertarium"=>1,
					"C8"=>1,
					"Google"=>1,
					"Yandex"=>1,
					"Adrock"=>1,
					"Holder"=>1,
					"Bigmir"=>1,
					"Redtram"=>1,
					"Marketgid"=>1,
					"Zapchast"=>1
				);
	protected $_bannerActions = array();
	protected $_id;
	protected $_channel;
	
	public function __construct($controller, $width, $height, $bannersArr = array(), $massive = true, $id = false, $channel = false){
		$this->_controller = $controller;
		$this->_width      = intval($width);
		$this->_height     = intval($height);
		$this->_beMassive  = $massive;
		$this->_id         = $id;
		$this->_channel         = $channel;
		if (!empty($bannersArr)){
			if (!is_array($bannersArr)) $bannersArr = array($bannersArr);
			$this->_bannersArr = $bannersArr;
		}
		$this->_bannerActions = array();
		foreach($this->_bannersArr as $bannerAct => $mass){
			for ($i=0;$i<$mass;$i++) $this->_bannerActions[] = $bannerAct;
		}
	}
	
	public function getJavascriptCode(){
		$code = '';
		$id   = md5($this->_width.$this->_height.rand(0, 10000));
		$code .= '<div id="'.$id.'"'.($this->_beMassive?' style="margin:0 auto;width:'.$this->_width.'px;height:'.$this->_height.'px;"':'').'></div>';
		
		$randKey = array_rand($this->_bannerActions);
		$bannerAction = "get".$this->_bannerActions[$randKey]."Banner";
		if (method_exists($this, $bannerAction)){
			$code .= '<script type="text/javascript">';
			$code .= '$(window).load(function() {';
			$code .= '$("#'.$id.'").html("';
			$code .= str_replace(array("\n", "\r"), array('',''),addslashes($this->$bannerAction()));
			$code .= '");';
			$code .= '})';
			$code .= '</script>';
		}
		return $code;
	}
	
	protected function getLocalBanner(){
		$banner = new Tools_Banner(STATIC_HOST_NAME, HOST_NAME, $this->_width, $this->_height, $this->_controller->usedBanners);
		$this->_controller->usedBanners[] = $banner->getFileName();
		return $banner->getJavascriptCode();
	}
	
	protected function getAdvertariumBanner(){
		$banner = new Tools_AdvertariumBanner($this->_width, $this->_height);
		return $banner->getJavascriptCode();
	}
	
	protected function getAdrockBanner(){
		$banner = new Tools_AdrockBanner($this->_width, $this->_height);
		return $banner->getJavascriptCode();
	}
	
	protected function getHolderBanner(){
		$banner = new Tools_HolderBanner($this->_width, $this->_height);
		return $banner->getJavascriptCode();
	}
	
	protected function getBigmirBanner(){
		$banner = new Tools_BigmirBanner($this->_width, $this->_height);
		return $banner->getJavascriptCode();
	}
	
	protected function getC8Banner(){
		$banner = new Tools_C8Banner(HOST_NAME, $this->_width, $this->_height);
		return $banner->getJavascriptCode();
	}
	
	protected function getZapchastBanner(){
		$banner = new Tools_ZapchastBanner(HOST_NAME, $this->_width, $this->_height);
		return $banner->getJavascriptCode();
	}
	
	protected function getGoogleBanner(){
		$banner = new Tools_GoogleBanner(HOST_NAME, $this->_width, $this->_height, $this->_id);
		return $banner->getJavascriptCode();
	}
	
	protected function getYandexBanner(){
		$banner = new Tools_YandexBanner(HOST_NAME, $this->_width, $this->_height, $this->_controller->usedBanners, $this->_id, $this->_channel);
		$this->_controller->usedBanners[] = $banner->getFileName();
		return $banner->getJavascriptCode();
	}
	
	protected function getRedtramBanner(){
		$banner = new Tools_RedtramBanner(HOST_NAME, $this->_width, $this->_height);
		return $banner->getJavascriptCode();
	}

	protected function getMarketgidBanner(){
		$banner = new Tools_MarketgidBanner(HOST_NAME, $this->_width, $this->_height);
		return $banner->getJavascriptCode();
	}
	protected function getAdmixerBanner(){
		$banner = new Tools_AdmixerBanner(HOST_NAME, $this->_width, $this->_height);
		return $banner->getJavascriptCode();
	}
}