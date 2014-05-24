<?php 
class Users_Form_Login extends Zend_Form{
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
		$this->addElement('text', 'username', array(
				'label' => 'Username:',
				'required' => true,
				'filters'    => array('StringTrim'),
				'class' => 'form-control',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-8')),
						//'Errors',
						array('Label',array('class'=>'col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
	
		$this->addElement('password', 'password', array(
				'label' => 'Password:',
				'required' => true,
				'class' => 'form-control',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-8')),
						//'Errors',
						array('Label',array('class'=>'col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
	
		$this->addElement('submit', 'submit', array(
				'ignore'	=> true,
				'label'		=> 'Login',
				'class'		=> 'btn btn-default',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-offset-3 col-sm-8')),
						//'Errors',
						//array('Label',array('class'=>'col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
	
	}
}
?>