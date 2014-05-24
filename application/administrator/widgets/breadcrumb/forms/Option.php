<?php 
class Params_Form_Option extends Zend_Form{
	public function init()
	{
		$this->addElement('radio', 'home', array(
				'label' => 'Show Home:',
				'value' => '1',
				'multiOptions' => array(
						'0' => 'No',
						'1' => 'Yes'
				),
				'label_class' => 'btn btn-default btn-sm',
				'separator'   => '',
				'decorators' =>  array(
						'ViewHelper',
						array(array('Value'=>'HtmlTag'), array('tag'=>'div','class'=>'btn-group btn-group-yes-no', 'data-toggle' => 'buttons')),
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class' => ' col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label', array('class' => 'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		$this->addElement('radio', 'home_icon', array(
				'label' => 'Show Home Icon:',
				'value' => '1',
				'multiOptions' => array(
						'0' => 'No',
						'1' => 'Yes'
				),
				'label_class' => 'btn btn-default btn-sm',
				'separator'   => '',
				'decorators' =>  array(
						'ViewHelper',
						array(array('Value'=>'HtmlTag'), array('tag'=>'div','class'=>'btn-group btn-group-yes-no', 'data-toggle' => 'buttons')),
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class' => ' col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label', array('class' => 'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addDisplayGroup(
				array('home', 'home_icon'),
				'group1',
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