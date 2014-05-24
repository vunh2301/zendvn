<?php 
class Admin_Form_Login extends Zend_Form{
	public function init()
	{

		$this->setMethod('post');
		$this->setAttribs(array(
				'role' => 'form',
		))->setDecorators(array(
				'FormElements',
				'Form'
		));
		$this->addElement('text', 'username', array(
				'placeholder' => 'Username',
				'required' => true,
				'filters'    => array('StringTrim'),
				'class' => 'form-control',
				'decorators' => array(
						'ViewHelper',
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
	
		$this->addElement('password', 'password', array(
				'placeholder' => 'Password',
				'required' => true,
				'class' => 'form-control',
				'decorators' => array(
						'ViewHelper',
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));

	
		$this->addElement('submit', 'submit', array(
				'ignore'	=> true,
				'label'		=> 'Login',
				'class'		=> 'btn btn-default',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div')),
						//'Errors',
						//array('Label',array('class'=>'col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
	
	}
}
?>