<?php 
class Users_Form_Group extends Zend_Form{
	
	public function init()
	{
		
		$this->setMethod('post');
		$this->setAttribs(array(
				'role' => 'form',
				'class' => 'form-horizontal'
		))->setDecorators(array(
				'FormElements',
				'Form'
		));
		
		
		// Group Detail
		$this->addElement("hidden","id");
		
		$this->addElement('text', 'title', array(
				'label' => 'Group Title: ',
				'required' => true,
				//'filters'    => array('StringTrim'), 
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						'Errors',
						array('Label',array('class'=>'col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group input-sm'))
				)
		));
		
		$this->addElement('select', 'parent_id', array(
				'label' => 'Group Parent: ',
				//'required' => true,
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						'Errors',
						array('Label',array('class'=>'col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group input-sm'))
				)
		));
	}
}
?>