<?php
class TablebookerAPI{
	//url
	protected $url = "//embed.tablebooker.be/";
	protected $restaurant_id;
	
	public function __construct($restaurant_id){
		$this->restaurant_id = $restaurant_id;
	}
	
	public function getReservationFrom($color = 'light', $lang = 'en'){
		// Embed background color
		if(!in_array($color, array('light','dark'))){
			$color = 'light';
		}
	
		// Embed language
		if($lang == 'current'){
			$current_lang = explode('-', get_bloginfo("language"));
			$lang = $current_lang[0];
		}
		if(!in_array($lang, array('nl', 'fr', 'en'))){
			$lang = 'en';
		}
		
		return '<iframe src="'.$this->url.'/'.$this->restaurant_id.'/'.$lang.'/'.$color.'" style="width: 100%; min-width: 120px; height: 440px; border: 0;" allowtransparency="true"></iframe>';
	}
}
?>