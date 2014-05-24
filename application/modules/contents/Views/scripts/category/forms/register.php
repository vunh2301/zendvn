<?php 
class Params_Form_Register extends Zend_Form{
	public function init()
	{
		// Select Parent

		
		$this->addElement('select', 'cid', array(
				'label' => 'Chose Category:',
				'required' => true,
				'multiOptions' => array(
						0 => 'No',
						1 => 'Yes'
				),
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => 'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group has-primary'))
				)
		)); 
		
		$this->addElement('text', 'param1', array(
				'label' => 'Param 1:',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => ' col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'param2', array(
				'label' => 'Param 2:',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => ' col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'param3', array(
				'label' => 'Param 3:',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => ' col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addDisplayGroup(
				array('cid', 'param1', 'param2'),
				'group1',
				array(
						'disableLoadDefaultDecorators' => true,
						'decorators' => array(
								'FormElements',
								array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-horizontal col-md-6'))
						)
							
				)
		);
		$this->addDisplayGroup(
				array('param3'),
				'group2',
				array(
						'disableLoadDefaultDecorators' => true,
						'decorators' => array(
								'FormElements',
								array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-horizontal col-md-6'))
						)
							
				)
		);
	}
}