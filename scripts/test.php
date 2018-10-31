<?php
require('config/config.php');
require('scripts/modules/HTTP.php');
require('scripts/modules/DB.php');
require('scripts/modules/Tools.php');
require('scripts/modules/Errors.php');

class Test {
	function __construct() {
		$this->http = new HTTP();
		$this->db = new DB();
		$this->tools = new Tools();

	}

	public function DBTest($params) {
		$this->db->connect_db($params);
	}

	
	public function TreeToFlatRef(&$tree, &$flat, $parent_id=null) {
		foreach ($tree as &$item) {
			$flat[] = [
				'id' => $item['id'],
				'parent_id' => $parent_id,
				'num' => $item['num']
			];
			if (isset($item['tree'])) {
				$this->TreeToFlatRef($item['tree'], $flat, $item['id']);
			}	
		}
	}
	
	public function FlatToTreeRef(&$flat, &$tree, $id=null) {
		$k = 0;
		foreach ($flat as &$item) {
			if (!(isset($id) || isset($item['parent_id'])) || $item['parent_id'] == $id) { 
				$tree[] = [
					'id' => $item['id'],
					'num' => $item['num'],
					'tree' => null
				];
				$this->FlatToTreeRef($flat, $tree[$k]['tree'], $item['id']);
				$k += 1;
			}
		}
	}
	
	public function TreeToFlat(&$tree, $childe_key, $parent_index, $index, $parent_id=null) {
		foreach ($tree as &$item) {
			$leaf = [];
			foreach ($item as $key=>$value) {
				if ($key != $childe_key) {	
					$leaf[$key] = $value;
				}
			}
			$leaf[$parent_index] = $parent_id;
			$flat[] = $leaf;
			
			if (isset($item[$childe_key])) {
				$leaves = $this->TreeToFlat($item[$childe_key], $childe_key, $parent_index, $index, $item[$index]);
				foreach ($leaves as $leaf) {
					$flat[] = $leaf;			
				}
			}	
		}
		return $flat;
	}
	
	public function FlatToTree(&$flat, $childe_key, $parent_index, $index, $id=null) {
		$tree = null;
		foreach ($flat as &$item) {
			if (!(isset($id) || isset($item[$parent_index])) || $item[$parent_index] == $id) { 
				$branch = [];
				foreach ($item as $key=>$value) {
					if ($key != $parent_index) {	
						$branch[$key] = $value;
					}
				}
				
				$childe = $this->FlatToTree($flat, $childe_key, $parent_index, $index, $item[$index]);
				if (isset($childe)) {	
					$branch[$childe_key] = $childe;
				}
				$tree[] = $branch;
			}
		}
		return $tree;
	}
	

}

$test = new Test();

// --------- DB test
$params = [
	'host' => '127.0.0.1',
	'db' => 'testdb',
	'usr' => 'root',
	'pass' => 'root',
	'charset' => 'utf8'
];
//$test->DBTest($params);


$tree = [
	[
		'id' => 1,
		'num' => '1',
		'tree' => [
			[
				'id' => 4,
				'num' => '1.1'
			],
			[
				'id' => 5,
				'num' => '1.2'
			],
			[
				'id' => 6,
				'num' => '1.3',
				'tree' => [
					[
						'id' => 9,
						'num' => '1.3.1'
					],
					[
						'id' => 10,
						'num' => '1.3.2'
					]
				]
			]
		]
	],
	[
		'id' => 2,
		'num' => "2",
		'tree' => [
			[
				'id' => 7,
				'num' => '2.1'
			],
			[
				'id' => 8,
				'num' => '2.2'
			]
		]
	],
	[
		'id' => 3,
		'num' => "3",
	]
];

$flat = $test->TreeToFlat($tree, 'tree', 'parent_id', 'id');
// print_r($flat);
$tree = $test->FlatToTree($flat, 'events', 'parent_id', 'id');
print_r($tree);

// $flat = [];
// $test->TreeToFlatRef($tree, $flat);
// print_r($flat);
// $tree = [];
// $test->FlatToTreeRef($flat, $tree);
// print_r($tree);


