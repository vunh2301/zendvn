<?php 
class Params_Form_Option extends Zend_Form{
	public function init()
	{
		// Select Parent
		$tblMenuLocation =  new Zend_Db_Table(array('name' => 'menu_type'));
		foreach ($tblMenuLocation->fetchAll($tblMenuLocation->select()->order('title')) as $rowMenu) 
				$selectLocation[$rowMenu->id] = $rowMenu->title;

		
		$this->addElement('select', 'menu_location', array(
				'label' => 'Chose Category:',
				'required' => true,
				'multiOptions' => (array)$selectLocation,
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div')),
						'Errors',
						array('Label',array('class'=>'small control-label')),
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
		
		$this->addElement('text', 'param4', array(
				'label' => 'Param 4:',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => ' col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'param5', array(
				'label' => 'Param 5:',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => ' col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'param6', array(
				'label' => 'Param 6:',
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
				array('param1', 'param2', 'param3'),
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
				array('param4', 'param5', 'param6'),
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