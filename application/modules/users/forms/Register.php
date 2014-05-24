<?php 
class Users_Form_Register extends Zend_Form{
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
		
		$this->addElement('text', 'real_name', array(
				'label' => 'Real Name:',
				'required' => true,
				'filters'    => array('StringTrim'),
				'class' => 'form-control',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-8')),
						'Errors',
						array('Label',array('class'=>'col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'username', array(
				'label' => 'Username:',
				'required' => true,
				'filters'    => array('StringTrim'),
				'class' => 'form-control',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-8')),
						'Errors',
						array('Label',array('class'=>'col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		$this->username->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'username')));
	
		$this->addElement('password', 'password', array(
				'label' => 'Password:',
				'required' => true,
				'class' => 'form-control',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-8')),
						'Errors',
						array('Label',array('class'=>'col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('password', 'password_confirm', array(
				'label' => 'Confirm Password:',
				'required' => true,
				'class' => 'form-control',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-8')),
						'Errors',
						array('Label',array('class'=>'col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		$this->password_confirm->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('Identical', false, array('token' => 'password'))
		->addErrorMessage('The passwords do not match');
		
		$this->addElement('text', 'email', array(
				'label' => 'Email Address:',
				'class' => 'form-control',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-8')),
						'Errors',
						array('Label',array('class'=>'col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		$this->email->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('EmailAddress')
		->addValidator('NotEmpty')
		->addValidator(new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'email')));
	
		$this->addElement('text', 'email_confirm', array(
				'label' => 'Confirm email Address:',
				'class' => 'form-control',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-8')),
						'Errors',
						array('Label',array('class'=>'col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->email_confirm->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->addValidator('Identical', false, array('token' => 'email'))
		->addErrorMessage('The email do not match');
		
		$this->addElement('submit', 'submit', array(
				'ignore'	=> true,
				'label'		=> 'Register',
				'class'		=> 'btn btn-default',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-offset-3 col-sm-8')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));

	}
}
?>