<?php 
class Params_Form_Option extends Zend_Form{
	public function init()
	{

		$this->addElement('radio', 'inverted', array(
				'label' => 'Inverted:',
				'value' => '0',
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
		
		$this->addElement('radio', 'hover', array(
				'label' => 'Hover:',
				'value' => '0',
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
		
		$this->addElement('select', 'fixed', array(
				'label' => 'Fixed:',
				'value' => '',
				'multiOptions' => array(
						'' => 'Default',
						'navbar-static-top' => 'Static top',
						'navbar-fixed-top' => 'Fixed to top',
						'navbar-fixed-bottom' => 'Fixed to bottom'
				),
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => ' col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'brand_name', array(
				'label' => 'Brand Name:',
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
				array('inverted', 'fixed'),
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
				array('hover', 'brand_name'),
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