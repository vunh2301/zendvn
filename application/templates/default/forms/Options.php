<?php 
class Templates_Form_Options extends Zend_Form{
	public function init()
	{

		
		$this->addElement('text', 'top', array(
				'label' => 'Top Positions:',
				'placeholder' => 'A|B|C|D|E|F',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class'=>'col-sm-8')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-4 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		$this->addElement('text', 'header', array(
				'label' => 'Header Positions:',
				'placeholder' => 'A|B|C|D|E|F',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class'=>'col-sm-8')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-4 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		$this->addElement('text', 'showcase', array(
				'label' => 'Showcase Positions:',
				'placeholder' => 'A|B|C|D|E|F',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class'=>'col-sm-8')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-4 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		$this->addElement('text', 'feature', array(
				'label' => 'Feature Positions:',
				'placeholder' => 'A|B|C|D|E|F',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class'=>'col-sm-8')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-4 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		$this->addElement('text', 'utility', array(
				'label' => 'Utility Positions:',
				'placeholder' => 'A|B|C|D|E|F',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class'=>'col-sm-8')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-4 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		$this->addElement('text', 'sidebar', array(
				'label' => 'Sidebar Positions:',
				'placeholder' => 'A|B|C',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class'=>'col-sm-8')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-4 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		$this->addElement('text', 'content_top', array(
				'label' => 'Content Top Positions:',
				'placeholder' => 'A|B|C',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class'=>'col-sm-8')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-4 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		$this->addElement('text', 'content_bottom', array(
				'label' => 'Content Bottom Positions:',
				'placeholder' => 'A|B|C',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class'=>'col-sm-8')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-4 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		$this->addElement('text', 'bottom', array(
				'label' => 'Bottom Positions:',
				'placeholder' => 'A|B|C|D|E|F',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class'=>'col-sm-8')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-4 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		$this->addElement('text', 'footer', array(
				'label' => 'Footer Positions:',
				'placeholder' => 'A|B|C|D|E|F',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class'=>'col-sm-8')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-4 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		$this->addElement('text', 'copyright', array(
				'label' => 'Copyright Positions:',
				'placeholder' => 'A|B|C|D|E|F',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class'=>'col-sm-8')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-4 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		
		$this->addDisplayGroup(
				array('top', 'header', 'showcase', 'feature', 'utility', 'sidebar', 'content_top', 'content_bottom', 'bottom', 'footer', 'copyright'),
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