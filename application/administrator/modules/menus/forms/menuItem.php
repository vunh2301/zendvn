<?php 
class Menus_Form_MenuItem extends Zend_Form{
	public function init()
	{
		$this->setMethod('post');
		$this->setAttribs(array('role' => 'form', 'class' => 'form-horizontal'))->setDecorators(array('FormElements', 'Form'));

		$this->addElement('hidden', 'type_module', array('readonly' => true, 'decorators' => array('ViewHelper')));
		$this->addElement('hidden', 'module_id', array('readonly' => true, 'decorators' => array('ViewHelper')));
		$this->addElement('hidden', 'controller', array('readonly' => true, 'decorators' => array('ViewHelper')));
		$this->addElement('hidden', 'pre_order', array('readonly' => true, 'decorators' => array('ViewHelper')));
		
		$this->addDisplayGroup(array('pre_order', 'type_module', 'module_id', 'controller'), 'hidden', array('decorators' => array('FormElements')));
		
		$this->addElement('text', 'type_title', array(
				'label' => 'Menu Type:',
				'required' => true,
				'class' => 'form-control',
				'readonly' => true,
				'decorators' => array(
						'ViewHelper',
						array(array('openAddon'=>'HtmlTag'), array('tag'=>'span', 'class'=>'input-group-btn', 'placement' => 'append', 'openOnly' => true)),
						array(array('openBtn'=>'HtmlTag'), array('tag'=>'button', 'type' => 'button', 'data-toggle' => 'modal', 'data-target' => '#menuTypeModal', 'class'=>'btn btn-default btn-primary', 'placement' => 'append', 'openOnly' => true)),
						array(array('icon'=>'HtmlTag'), array('tag'=>'span', 'class'=>'glyphicon glyphicon-folder-open', 'placement' => 'append')),
						array(array('closeBtn'=>'HtmlTag'), array('tag'=>'button', 'placement' => 'append', 'closeOnly' => true)),
						array(array('closeAddon'=>'HtmlTag'), array('tag'=>'span', 'placement' => 'append', 'closeOnly' => true)),
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'input-group')),
						array(array('warp2'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group has-primary'))
				)
		));
		
		$this->addElement('select', 'template_id', array(
				'label' => 'Template Style: ',
				'required' => true,
				"registerInArrayValidator" => false,
				'class' => 'form-control input-sm',
				'value' => 0,
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => 'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'link', array(
				'label' => 'Direct Url: ',
				'readonly' => true,
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => ' col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('select', 'target_window', array(
				'label' => 'Target Window: ',
				'value' => 0,
				'multiOptions' => array(
						0 => 'Inhereit',
						1 => 'New Tab',
						2 => 'Popup',
						3 => 'Modal'
				),
				'required' => true,
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => 'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'title', array(
				'label' => 'Title:',
				'required' => true,
				'class' => 'form-control',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div')),
						array('Errors',array('class' => 'text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group has-primary'))
				)
		));
		
		$this->addElement('text', 'alias', array(
				'label' => 'Alias:',
				'required' => true,
				'placeholder' => 'Auto generate from title',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div')),
						array('Errors',array('class' => 'text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('radio', 'status', array(
				'label' => 'Status:',
				'value' => 'publish',
				'multiOptions' => array(
						'unpublish' => 'Unpublish',
						'publish' => 'Publish'
				),
				'label_class' => 'btn btn-default btn-sm',
				'separator'   => '',
				'decorators' =>  array(
						'ViewHelper',						
						array(array('Value'=>'HtmlTag'), array('tag'=>'div','class'=>'btn-group btn-group-yes-no', 'data-toggle' => 'buttons')),
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class' => 'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label', array('class' => 'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('radio', 'home', array(
				'label' => 'Home Page:',
				'value' => 0,
				'multiOptions' => array(
						0 => 'No',
						1 => 'Yes'
				),
				'label_class' => 'btn btn-default btn-sm',
				'separator'   => '',
				'decorators' =>  array(
						'ViewHelper',
						array(array('Value'=>'HtmlTag'), array('tag'=>'div','class'=>'btn-group btn-group-yes-no', 'data-toggle' => 'buttons')),
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class' => 'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label', array('class' => 'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('select', 'menu_type_id', array(
				'label' => 'Menu Location: ',
				'required' => true,
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => 'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('select', 'parent_id', array(
				'label' => 'Parent: ',
				'required' => true,
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => 'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'order', array(
				'label' => 'Ordering: ',
				'required' => true,
				'class' => 'form-control input-sm',
				'readonly' => true,
				'decorators' => array(
						'ViewHelper',
						array(array('openAddon'=>'HtmlTag'), array('tag'=>'span', 'class'=>'input-group-btn', 'placement' => 'append', 'openOnly' => true)),
						array(array('openBtn'=>'HtmlTag'), array('tag'=>'button', 'type' => 'button', 'data-toggle' => 'modal', 'data-target' => '#orderModal', 'class'=>'btn btn-default btn-sm', 'placement' => 'append', 'openOnly' => true, 'onclick' => '$(\'.dd\').nestable({maxDepth: 1});')),
						array(array('icon'=>'HtmlTag'), array('tag'=>'span', 'class'=>'glyphicon glyphicon-align-right', 'placement' => 'append')),
						array(array('closeBtn'=>'HtmlTag'), array('tag'=>'button', 'placement' => 'append', 'closeOnly' => true)),
						array(array('closeAddon'=>'HtmlTag'), array('tag'=>'span', 'placement' => 'append', 'closeOnly' => true)),
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'input-group')),
						array(array('warp2'=>'HtmlTag'), array('tag'=>'div', 'class' => 'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'id', array(
				'label' => 'Menu ID:',
				'filters'    => array('StringTrim'),
				'class' => 'form-control input-sm',
				'readonly' => true,
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'col-sm-3 control-label input-sm')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('textarea', 'metadesc', array(
				'label' => 'Meta Description: ',
				'belongsTo' => 'params',
				'filters'    => array('HtmlEntities'),
				'rows' => 3,
				'class' => 'form-control',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => ' col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('textarea', 'metakey', array(
				'label' => 'Meta Keywords: ',
				'belongsTo' => 'params',
				'filters'    => array('StringTrim'),
				'rows' => 3,
				'class' => 'form-control',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => ' col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'author', array(
				'label' => 'Meta Author: ',
				'belongsTo' => 'params',
				'filters'    => array('StringTrim'),
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => ' col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('select', 'robots', array(
				'label' => 'Robots: ',
				'belongsTo' => 'params',
				'class' => 'form-control input-sm',
				'value' => 'global',
				'multiOptions' => array(
						'global' => 'Use Global',
						'index, follow' => 'Index, Follow',
						'noindex, follow' => 'No index, follow',
						'index, nofollow' => 'Index, No follow',
						'noindex, nofollow' => 'No index, no follow',
				),
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => 'col-sm-9')),
						'Errors',
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addDisplayGroup(
				array('link', 'status', 'home', 'menu_type_id', 'parent_id', 'order'),
				'detail',
				array(
						'disableLoadDefaultDecorators' => true,
						'decorators' => array(
								'FormElements'
						)
							
				)
		);
		$this->addDisplayGroup(
				array('template_id', 'target_window', 'metadesc', 'metakey', 'author', 'robots'),
				'detail2',
				array(
						'disableLoadDefaultDecorators' => true,
						'decorators' => array(
								'FormElements'
						)
							
				)
		);
		
	}
	
}