<?php 
class Menus_Form_Menu extends Zend_Form{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttribs(array('role' => 'form', 'class' => 'form-horizontal'))->setDecorators(array('FormElements', 'Form'));
		
		$textDecorators = array(
				'ViewHelper',
				array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
				array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
				array('Label',array('class'=>'small col-sm-3 control-label')),
				array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
		
		);
		
		$this->addElement('text', 'title', array(
				'label' => 'Title:',
				'required' => true,
				'class' => 'form-control input-sm',
				'decorators' => $textDecorators
		));
		$this->addElement('textarea', 'description', array(
				'label' => 'Description: ',
				'filters'    => array('HtmlEntities'),
				'rows' => 3,
				'class' => 'form-control input-sm',
				'decorators' => $textDecorators
		));
		
	}
	
}