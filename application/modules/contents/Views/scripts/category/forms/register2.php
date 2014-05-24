<?php 
class Params_Form_Register2 extends Zend_Form{
	public function init()
	{
		$this->addElement('text', 'param4', array(
				'label' => 'Param 4:',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						'Errors',
						array('Label',array('class'=>'col-sm-3 control-label input-sm')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
		
				)
		));
		
		$this->addElement('text', 'param5', array(
				'label' => 'Param 5:',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						'Errors',
						array('Label',array('class'=>'col-sm-3 control-label input-sm')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
		
				)
		));
		
		$this->addElement('text', 'param6', array(
				'label' => 'Param 6:',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						'Errors',
						array('Label',array('class'=>'col-sm-3 control-label input-sm')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
		
				)
		));
	}
}