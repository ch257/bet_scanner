<?php
class Parser {
	function __construct() {
		$this->tools = new Tools();
		$this->sport_ltree_by_id = [];
		$this->event_ltree_by_id = [];
	}
	
	private function _fillSports(&$sports, &$events, &$custom_factors) {
		foreach ($custom_factors as &$custom_factor) {
			$event_id = $custom_factor['e'];
			$event_ltree = $this->event_ltree_by_id[$event_id];
			
			$this->tools->AppendByLtree('customFactors', $custom_factor, $events, $event_ltree, 'events');
			// $event_item = $this->tools->GetByLtree($events, $event_ltree, 'events');
			// print_r($event_item);
		}
	}
	
	private function _parseFonbetLink($content) {
		$sports = $this->tools->FlatToTree($content['sports'], 'segments', 'parentId', 'id', $this->sport_ltree_by_id);
		$events = $this->tools->FlatToTree($content['events'], 'events', 'parentId', 'id', $this->event_ltree_by_id);
		$custom_factors = $content['customFactors'];
		
		$this->_fillSports($sports, $events, $custom_factors);
		
		// return $sports;
		return $events;
	}
	
	public function ParseContent($content, $params) {
		if ($params['response']['parser'] == '_parseFonbetLink') {
			return $this->_parseFonbetLink($content);
		}
	}
	
}