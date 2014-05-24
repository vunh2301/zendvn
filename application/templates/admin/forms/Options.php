<?php 
class Templates_Form_Options extends Zend_Form{
	public function init()
	{

		
		$this->addElement('select', 'theme', array(
				'label' => 'Theme:',
				'value' => 'default',
				'required' => true,
				'multiOptions' => array(
						'default' => 'Default',
						'amelia' => 'Amelia',
						'cerulean' => 'Cerulean',
						'cosmo' => 'Cosmo',
						'cyborg' => 'Cyborg',
						'darkly' => 'Darkly',
						'flatly' => 'Flatly',
						'slate' => 'Slate',
				),
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group')),
						array(array('Col'=>'HtmlTag'), array('tag'=>'div','class'=>'col-md-6 form-horizontal'))
				)
		));
		
		
		$this->addDisplayGroup(
				array('theme'),
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