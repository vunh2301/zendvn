<?php

class Zendvn_Db_Table_NestedSet extends Zend_Db_Table_Abstract
{
	protected $_ordering;
	
	public function removeNode($id, $removeChilds = true){
		if($removeChilds == true) 
			$this->removeBranch($id);
		else
			$this->removeOne($id);
	}
	
	public function insertNode($data, $position, $destination) {
		switch ($position) {
			case 'right'	: return $this->insertRight($data, $destination); break;
			case 'left'		: return $this->insertLeft($data, $destination); break;
			case 'after'	: return $this->insertAfter($data, $destination); break;
			case 'before'	: return $this->insertBefore($data, $destination); break;
		}
	}

	public function moveNode($id, $position, $destination){
		switch ($position) {
			case 'up'		: $this->moveUp($id); break;
			case 'down'		: $this->moveDown($id); break;
			case 'right'	: $this->moveRight($id, $destination); break;
			case 'left'		: $this->moveLeft($id, $destination); break;
			case 'after'	: $this->moveAfter($id, $destination); break;
			case 'before'	: $this->moveBefore($id, $destination); break;
		}
	}
	
	public function updateBranch(array $data, $id){
		$nodeUpdate 	=  $this->getNode($id);
		$this->_db->update($this->_name, $data, '(lft >= ' . $nodeUpdate->lft . ' AND rgt <= ' . $nodeUpdate->rgt . ')');
	}
	
	public function getChilds($id){
		$nodeUpdate 	=  $this->getNode($id);
		return $this->fetchAll('(lft > ' . $nodeUpdate->lft . ' AND rgt < ' . $nodeUpdate->rgt . ')');
	}
	
	public function getNodeOrdering($id, $parentId){
		if(!isset($this->_ordering[$parentId])){
			$orderList = $this->fetchAll($this->select()->where('parent_id = ?', $parentId)->order('lft'));
			foreach($orderList as $index => $item){
				$this->_ordering[$parentId][$item->id] = $index;
			}
		}
		return $this->_ordering[$parentId][$id];
	}
	
	public function getNode($id){
		return $this->fetchRow($this->select()->where("id = ?", $id));
	}
	
	public function getRoot(){
		return $this->fetchRow($this->select()->where("lft = ?", 0));
	}
	
	public function getNodeByLeft($left){
		return $this->_db->fetchRow($this->_db->select()->where("lft = ?", $left));
	}
	
	protected function widthNode($lft, $rgt){
		return $rgt - $lft + 1;
	}
	
	protected function insertRight(array $data, $parent_id = 0){
		if($parent_id == 0){
			$parentInfo 	= $this->getRoot();
		}else{
			$parentInfo 	= $this->getNode($parent_id);
		}
		$parentRight 	= ($parentInfo->rgt) ? $parentInfo->rgt : 0;

		$this->_db->update($this->_name, array("lft" => new Zend_db_Expr('lft+2')), 'lft > '. $parentRight);
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt+2')), 'rgt >= '. $parentRight);
		
		$data['parent_id']	= $parent_id;
		$data['lft'] 		= $parentRight;
		$data['rgt'] 		= $parentRight + 1;
		$data['level'] 		= $parentInfo->level + 1;
		return $this->insert($data);		
	}
	
	protected function insertLeft(array $data, $parent_id = 0){
		if($parent_id == 0){
			$parentInfo 	= $this->getRoot();
		}else{
			$parentInfo 	= $this->getNode($parent_id);
		}
		$parentLeft 	= $parentInfo->lft;

		$this->_db->update($this->_name, array("lft" => new Zend_db_Expr('lft + 2')), 'lft > ' . $parentLeft);
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt + 2')), 'rgt >= ' . ($parentLeft + 1));
	
		$data['parent_id']	= $parent_id;
		$data['lft'] 		= $parentLeft + 1;
		$data['rgt'] 		= $parentLeft + 2;
		$data['level'] 		= $parentInfo->level + 1;
		return $this->insert($data);
	}
	
	protected function insertAfter(array $data, $brother_id){
		$brotherInfo =  $this->getNode($brother_id);
		$parentInfo =  $this->getNode($brotherInfo->parent_id);

		$this->_db->update($this->_name, array("lft" => new Zend_db_Expr('lft + 2')), 'lft > '. $brotherInfo->rgt);
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt + 2')), 'rgt > '. $brotherInfo->rgt);
	
		$data['parent_id']	= $brotherInfo->parent_id;
		$data['lft'] 		= $brotherInfo->rgt + 1;
		$data['rgt'] 		= $brotherInfo->rgt + 2;
		$data['level'] 		= $parentInfo->level + 1;
		return $this->insert($data);
	}
	
	protected function insertBefore(array $data, $brother_id){
		$brotherInfo =  $this->getNode($brother_id);
		$parentInfo =  $this->getNode($brotherInfo->parent_id);

		$this->_db->update($this->_name, array("lft" => new Zend_db_Expr('lft + 2')), 'lft >= '. $brotherInfo->lft);
		$this->_db->update($this->_name, array("rgt"=> new Zend_db_Expr('rgt + 2')), 'rgt >= ' . ($brotherInfo->lft + 1));
	
		$data['parent_id']	= $brotherInfo->parent_id;
		$data['lft'] 		= $brotherInfo->lft;
		$data['rgt'] 		= $brotherInfo->lft + 1;
		$data['level'] 		= $parentInfo->level + 1;
		return $this->insert($data);
	}
	
	public function moveBefore($id, $brother_id){
		$infoMoveNode 	= $this->getNode($id);
		$lftMoveNode 	= $infoMoveNode->lft;
		$rgtMoveNode 	= $infoMoveNode->rgt;
		$widthMoveNode 	= $this->widthNode($lftMoveNode, $rgtMoveNode);
	
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt - ' . $rgtMoveNode), "lft" => new Zend_db_Expr('lft - ' . $lftMoveNode)), 'lft BETWEEN ' . $lftMoveNode . ' AND ' . $rgtMoveNode);
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt - ' . $widthMoveNode)), 'rgt > ' . $rgtMoveNode);
		$this->_db->update($this->_name, array("lft" => new Zend_db_Expr('lft - ' . $widthMoveNode)), 'lft > ' . $rgtMoveNode);

		$infoBrotherNode 	= $this->getNode($brother_id);
		$lftBrotherNode 	= $infoBrotherNode->lft;

		$this->_db->update($this->_name, array("lft" => new Zend_db_Expr('lft + ' . $widthMoveNode)), 'lft >= ' . $lftBrotherNode . ' AND rgt > 0');
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt + ' . $widthMoveNode)), 'rgt >= ' . $lftBrotherNode);
	
		
		$infoParentNode 	= $this->getNode($infoBrotherNode->parent_id);
		$levelMoveNode 		= $infoMoveNode->level;
		$levelParentNode	= $infoParentNode->level;
		$newLevelMoveNode  	= $levelParentNode + 1;

		$this->_db->update($this->_name, array("level"	=> new Zend_db_Expr('level - ' . $levelMoveNode . ' + ' . $newLevelMoveNode)), 'rgt <= 0');
	
		$newParent 	= $infoParentNode->id;
		$newLeft 	= $infoBrotherNode->lft;
		$newRight 	= $infoBrotherNode->lft + $widthMoveNode - 1;

		$this->_db->update($this->_name, array("parent_id"	=> $newParent, "lft" => $newLeft, "rgt" => $newRight), 'id = ' . $id);
		$this->_db->update($this->_name, array("lft" => new Zend_db_Expr("lft + " . $newLeft), "rgt" => new Zend_db_Expr("rgt + " . $newRight)), 'rgt < 0');
	}
	
	protected function moveAfter($id, $brother_id){
		$infoMoveNode 	= $this->getNode($id);	
		$lftMoveNode	= $infoMoveNode->lft;
		$rgtMoveNode 	= $infoMoveNode->rgt;
		$widthMoveNode 	= $this->widthNode($lftMoveNode, $rgtMoveNode);
	
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr("rgt - " . $rgtMoveNode), "lft" => new Zend_db_Expr("lft - " . $lftMoveNode)), 'lft BETWEEN ' . $lftMoveNode . ' AND ' . $rgtMoveNode);
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr("rgt - " . $widthMoveNode)), 'rgt > ' . $rgtMoveNode);
		$this->_db->update($this->_name, array("lft" => new Zend_db_Expr("lft - " . $widthMoveNode)), 'lft > ' . $rgtMoveNode);
	
		$infoBrotherNode 	= $this->getNode($brother_id);
		$rgtBrotherNode 	= $infoBrotherNode->rgt;

		$this->_db->update($this->_name, array("lft" => new Zend_db_Expr("lft + " . $widthMoveNode)), 'lft > ' . $rgtBrotherNode . ' AND rgt > 0');
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr("rgt + " . $widthMoveNode)), 'rgt > ' . $rgtBrotherNode);
	
		$infoParentNode 	= $this->getNode($infoBrotherNode->parent_id);
		$levelMoveNode 		= $infoMoveNode->level;
		$levelParentNode	= $infoParentNode->level;
		$newLevelMoveNode  	= $levelParentNode + 1;

		$this->_db->update($this->_name, array("level" => new Zend_db_Expr('level - ' . $levelMoveNode . ' + ' . $newLevelMoveNode)), 'rgt <= 0');
	
		$newParent 	= $infoParentNode->id;
		$newLeft 	= $infoBrotherNode->rgt + 1;
		$newRight 	= $infoBrotherNode->rgt + $widthMoveNode;
	
		$this->_db->update($this->_name, array("parent_id" => $newParent, "lft" => $newLeft, "rgt" => $newRight), 'id = ' . $id);
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt + ' . $newRight), "lft" => new Zend_db_Expr('lft + ' . $newLeft)), 'rgt < 0');
	}
	
	protected function moveLeft($id, $parent_id){
		$infoMoveNode = $this->getNode($id);	
		$lftMoveNode = $infoMoveNode->lft;
		$rgtMoveNode = $infoMoveNode->rgt;
		$widthMoveNode = $this->widthNode($lftMoveNode, $rgtMoveNode);
	
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt - ' . $rgtMoveNode), "lft" => new Zend_db_Expr('lft - ' . $lftMoveNode)), 'lft BETWEEN ' . $lftMoveNode . ' AND ' . $rgtMoveNode);
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt - ' . $widthMoveNode)), 'rgt > ' . $rgtMoveNode);
		$this->_db->update($this->_name, array("lft" => new Zend_db_Expr('lft - ' . $widthMoveNode)), 'lft > ' . $rgtMoveNode);
	
		$infoParentNode = $this->getNode($parent_id);
		$lftParentNode = $infoParentNode->lft;
	
		$this->_db->update($this->_name, array("lft" => new Zend_db_Expr('lft + ' . $widthMoveNode)), 'lft > ' . $lftParentNode.' AND rgt > 0 ');	
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt + ' . $widthMoveNode)), 'rgt > ' . $lftParentNode);
	
		$levelMoveNode 		= $infoMoveNode->level;
		$levelParentNode	= $infoParentNode->level;
		$newLevelMoveNode  	= $levelParentNode + 1;
	
		$this->_db->update($this->_name, array("level" => new Zend_db_Expr('level - '. $levelMoveNode . ' + ' . $newLevelMoveNode)), 'rgt <= 0');
	
		$newParent 	= $infoParentNode->id;
		$newLeft 	= $infoParentNode->lft + 1;
		$newRight 	= $infoParentNode->lft + $widthMoveNode;
	
		$this->_db->update($this->_name, array("parent_id" => $newParent, "lft" => $newLeft, "rgt" => $newRight), 'id = ' . $id);
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt + ' . $newRight), "lft" => new Zend_db_Expr('lft + ' . $newLeft)), 'rgt < 0');
	}
	
	protected function moveRight($id, $parent_id){
		$infoMoveNode = $this->getNode($id);	
		$lftMoveNode = $infoMoveNode->lft;
		$rgtMoveNode = $infoMoveNode->rgt;
		$widthMoveNode = $this->widthNode($lftMoveNode, $rgtMoveNode);
	
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt - ' . $rgtMoveNode), "lft" => new Zend_db_Expr('lft - ' . $lftMoveNode)), 'lft BETWEEN ' . $lftMoveNode . ' AND ' . $rgtMoveNode);
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt - ' . $widthMoveNode)), 'rgt > ' . $rgtMoveNode);
		$this->_db->update($this->_name, array("lft" => new Zend_db_Expr('lft - ' . $widthMoveNode)), 'lft > ' . $rgtMoveNode);
	
		$infoParentNode = $this->getNode($parent_id);
		$rgtParentNode = $infoParentNode->rgt;
	
		$this->_db->update($this->_name, array("lft" => new Zend_db_Expr('lft + ' . $widthMoveNode)), 'lft >= ' . $rgtParentNode . ' AND rgt > 0');
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt + ' . $widthMoveNode)), 'rgt >= ' . $rgtParentNode);
	
		$levelMoveNode 		= $infoMoveNode->level;
		$levelParentNode	= $infoParentNode->level;
		$newLevelMoveNode  	= $levelParentNode + 1;

		$this->_db->update($this->_name, array("level" => new Zend_db_Expr('level - ' . $levelMoveNode . ' + ' . $newLevelMoveNode)), 'rgt <= 0');
	
		$newParent 	= $infoParentNode->id;
		$newLeft 	= $infoParentNode->rgt;
		$newRight 	= $infoParentNode->rgt + $widthMoveNode - 1;

		$this->_db->update($this->_name, array("parent_id" => $newParent, "lft" => $newLeft, "rgt" => $newRight), 'id = ' . $id);
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt + ' . $newRight), "lft" => new Zend_db_Expr('lft + ' . $newLeft)), 'rgt <0');
	}
	
	protected function moveUp($id){
		$nodeInfo 	= $this->getNode($id);
		$parentInfo = $this->getNode($nodeInfo->parent_id);
		$select		= $this->_db->select()->where("lft < ?", $nodeInfo->lft)->where('parent_id = ?', $nodeInfo->parent_id)->order("lft DESC");
		$nodeBrother = $this->_db->fetchRow($select);
		if(!empty($nodeBrother)) $this->moveBefore($id, $nodeBrother->id);
	}
	
	protected function moveDown($id){
		$nodeInfo 	= $this->getNode($id);
		$parentInfo = $this->getNode($nodeInfo->parent_id);
		$select		= $this->_db->select()->where("lft > ?", $nodeInfo->lft)->where('parent_id = ?', $nodeInfo->parent_id)->order("lft ASC");
		$nodeBrother = $this->_db->fetchRow($select);
		if(!empty($nodeBrother)) $this->moveAfter($id, $nodeBrother->id);
	}
	
	protected function removeOne($id){
		$nodeInfo = $this->getNode($id);
		$select = $this->_db->select()->where('parent_id = ?', $nodeInfo->id)->order('lft ASC');
		$result	= $this->_db->fetchAll($select);
		if ($result->count() > 0){
			foreach ($result as $k => $v) $childIds[] = $v->id;
		}else{
			$this->removeNode($nodeInfo->id);
		}
		if(count($childIds) > 0){
			rsort($childIds);
			foreach ($childIds as $childId){
				$this->movetAfter($childId, $nodeInfo->id);
			}
			$this->removeBranch($nodeInfo->id);
		}
	}
	
	protected function removeBranch($id){
		$nodeRemove 	=  $this->getNode($id);	
		$rgtNodeRemove 		= $nodeRemove->rgt;
		$lftNodeRemove 		= $nodeRemove->lft;
		$widthNodeRemove 	= $this->widthNode($lftNodeRemove, $rgtNodeRemove);

		$allNodeDelete = $this->fetchAll($this->select()->where('lft >= ' . $lftNodeRemove . ' AND lft <= ' . $rgtNodeRemove));
		if($allNodeDelete->count() > 0){
			foreach($allNodeDelete as $nodeDelete){
				$nodeDelete->delete();
			}
		}
		$this->_db->update($this->_name, array("lft" => new Zend_db_Expr('lft - ' . $widthNodeRemove)), 'lft >= ' . $rgtNodeRemove);
		$this->_db->update($this->_name, array("rgt" => new Zend_db_Expr('rgt - ' . $widthNodeRemove)), 'rgt >= ' . $rgtNodeRemove);
	}

}
