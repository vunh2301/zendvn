<?php 
class Contents_Form_Category extends Zend_Form{
	
	public function init()
	{
		$toolbar = 'code fullscreen preview print | undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | forecolor backcolor emoticons';
		
		$this->getView()->headScript()->appendScript('
			$(function(){
				tinymce.init({
				    selector: "textarea.tinyeditor",
				    theme: "modern",
				    menubar: false,
				    height: 300,
					relative_urls : false,
				    plugins: [
				         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
				         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
				         "save table contextmenu directionality emoticons template paste textcolor readmore moxiemanager"
				   ],
				   //content_css: "css/content.css",
				   toolbar: "' . $toolbar . '",
				   style_formats: [
				        {title: "Bold text", inline: "strong"},
				        {title: "Red text", inline: "span", styles: {color: "#ff0000"}},
				        {title: "Red header", block: "h1", styles: {color: "#ff0000"}},
				        {title: "Example 1", inline: "span", classes: "example1"},
				        {title: "Example 2", inline: "span", classes: "example2"},
				        {title: "Table styles"},
				        {title: "Table row 1", selector: "tr", classes: "tablerow1"}
				    ]
				 });
			});
		','text/javascript')->appendFile($this->getView()->baseUrl("plugins/tinyeditor/tinymce.min.js"));
		$this->getView()->headScript()->appendFile($this->getView()->baseUrl("templates/admin/js/holder.js"));
		$this->getView()->headScript()->appendFile($this->getView()->baseUrl("templates/admin/js/jasny-bootstrap.min.js"));
		$this->getView()->headLink()->appendStylesheet($this->getView()->baseUrl("templates/admin/css/jasny-bootstrap.min.css"));
		$this->setMethod('post');
		$this->setAttribs(array(
				'role' => 'form',
				'class' => 'form-horizontal',
				'enctype', 'multipart/form-data'
		))->setDecorators(array(
				'FormElements',
				'Form'
		));
		
		
		// Group Detail
		$this->addElement('hidden', 'pre_order', array('decorators' => array('ViewHelper')));
		$this->addElement('hidden', 'metadata', array('decorators' => array('ViewHelper')));
		$this->addElement('hidden', 'created_user_id', array('decorators' => array('ViewHelper')));
		$this->addElement('hidden', 'modified_user_id', array('decorators' => array('ViewHelper')));
		
		$this->addDisplayGroup(
				array('pre_order', 'created_user_id', 'modified_user_id'),
				'hidden',
				array(
						'disableLoadDefaultDecorators' => true,
						'decorators' => array(
								'FormElements'
						)
							
				)
		);
		
		$this->addElement('text', 'title', array(
				'label' => 'Title: ',
				'required' => true,
				'filters'    => array('StringTrim'), 
				'class' => 'form-control',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group has-primary'))
				)
		));

		$this->addElement('text', 'alias', array(
				'label' => 'Alias: ',
				//'required' => true,
				'filters'    => array(
						'StringTrim',
						'StringToLower',
				),
				'placeholder' => 'Auto generate from title',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('file', 'image', array(
				'label' => 'Thumbnail: ',
				'destination' => PUBLISH_PATH . '/modules/contents/images',
				'decorators' =>  array(
						array('File',array('style' => 'display:none;')),
						array(array('Callback'=>'callback'), array('callback' => array($this, 'buildImageElement'))),
						array(array('warp'=>'HtmlTag'), array('tag'=>'div')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		$this->image->addValidator('Size', false, 2048000);
		$this->image->addValidator('Extension', false, 'jpg,png,gif');
		

		$this->addElement('textarea', 'description', array(
				'label' => 'Description: ',
				'filters'    => array('StringTrim'),
				'rows' => 3,
				'class' => 'form-control tinyeditor',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));

		$this->addElement('radio', 'status', array(
				'label' => 'Status: ',
				'value' => 'publish',
				'multiOptions'	=> array(
					'unpublish'	=> 'Unpublish',
					'publish' 	=> 'Publish',
				),
				'label_class' => 'btn btn-default btn-sm',
				'separator'   => '',
				'decorators' =>  array(
						'ViewHelper',						
						array(array('Value'=>'HtmlTag'), array('tag'=>'div','class'=>'btn-group btn-group-yes-no', 'data-toggle' => 'buttons')),
						array(array('warp'=>'HtmlTag'), array('tag'=>'div')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label', array('class' => 'small')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));

		
		$this->addElement('select', 'parent_id', array(
				'label' => 'Parent: ',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'order', array(
				'label' => 'Ordering: ',
				//'required' => true,
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
						array(array('warp2'=>'HtmlTag'), array('tag'=>'div')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addDisplayGroup(
				array('image', 'parent_id', 'status', 'order'),
				'category',
				array(
						'disableLoadDefaultDecorators' => true,
						'decorators' => array(
								'FormElements'
						)
							
				)
		);
		
		$this->addElement('textarea', 'metadesc', array(
				'label' => 'Meta Description: ',
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
				'belongsTo' => 'metadata',
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
				'belongsTo' => 'metadata',
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
				array('metadesc', 'metakey', 'author', 'robots'),
				'seo',
				array(
						'disableLoadDefaultDecorators' => true,
						'decorators' => array(
								'FormElements'
						)
							
				)
		);
		
		$this->addElement('text', 'created_date', array(
				'label' => 'Created Date: ',
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
		
		$this->addElement('text', 'created_user', array(
				'label' => 'Created by: ',
				'readonly' => true,
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
		
		$this->addElement('text', 'modified_date', array(
				'label' => 'Modified Date: ',
				'readonly' => true,
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => 'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'modified_user', array(
				'label' => 'Modified by: ',
				'readonly' => true,
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
		
		$this->addElement('text', 'hits', array(
				'label' => 'Hits:',
				'class' => 'form-control input-sm',
				'value' => 0,
				'readonly' => true,
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => ' col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		

		$this->addElement('text', 'id', array(
				'label' => 'ID:',
				'class' => 'form-control input-sm',
				'readonly' => true,
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag' => 'div', 'class' => ' col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 text-danger clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addDisplayGroup(
				array('created_date', 'created_user', 'modified_date', 'modified_user', 'hits', 'id'),
				'publish',
				array(
						'disableLoadDefaultDecorators' => true,
						'decorators' => array(
								'FormElements'
						)
							
				)
		);
	}
	
	public static function buildImageElement($content, $element, array $options){
		$return = '
		<div class="fileinput fileinput-new" data-provides="fileinput" style="margin-bottom: 0;">
			<div class="fileinput-new thumbnail" style="height: 130px;">
				<img ' . ($element->getAttrib('src') ? 'src="' . $element->getAttrib('src') . '"' : 'data-src="holder.js/120x120" alt="130x130"') .'/>
  			</div>
			<div class="fileinput-preview fileinput-exists thumbnail" data-trigger="fileinput" style="max-height: 130px;"></div>
			<div>
				<span class="btn btn-default btn-file btn-sm">
					<span class="fileinput-new">Select image</span>
					<span class="fileinput-exists">Change</span>
					<input type="file" name="' . $element->getName() .'" id="' . $element->getId() .'" label_class="btn btn-default btn-sm">
				</span>
				<a href="#" class="btn btn-default fileinput-exists btn-sm" data-dismiss="fileinput">Remove</a>
			</div>
		</div>
		';
		return $return;
	}
}
?>