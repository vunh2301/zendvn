<?php 
class Users_Form_Profile extends Zend_Form{
	protected $timezones = array (
			'(GMT-11:00) Midway Island' => 'Pacific/Midway',
			'(GMT-11:00) Samoa' => 'Pacific/Samoa',
			'(GMT-10:00) Hawaii' => 'Pacific/Honolulu',
			'(GMT-09:00) Alaska' => 'US/Alaska',
			'(GMT-08:00) Pacific Time (US &amp; Canada)' => 'America/Los_Angeles',
			'(GMT-08:00) Tijuana' => 'America/Tijuana',
			'(GMT-07:00) Arizona' => 'US/Arizona',
			'(GMT-07:00) Chihuahua' => 'America/Chihuahua',
			'(GMT-07:00) La Paz' => 'America/Chihuahua',
			'(GMT-07:00) Mazatlan' => 'America/Mazatlan',
			'(GMT-07:00) Mountain Time (US &amp; Canada)' => 'US/Mountain',
			'(GMT-06:00) Central America' => 'America/Managua',
			'(GMT-06:00) Central Time (US &amp; Canada)' => 'US/Central',
			'(GMT-06:00) Guadalajara' => 'America/Mexico_City',
			'(GMT-06:00) Mexico City' => 'America/Mexico_City',
			'(GMT-06:00) Monterrey' => 'America/Monterrey',
			'(GMT-06:00) Saskatchewan' => 'Canada/Saskatchewan',
			'(GMT-05:00) Bogota' => 'America/Bogota',
			'(GMT-05:00) Eastern Time (US &amp; Canada)' => 'US/Eastern',
			'(GMT-05:00) Indiana (East)' => 'US/East-Indiana',
			'(GMT-05:00) Lima' => 'America/Lima',
			'(GMT-05:00) Quito' => 'America/Bogota',
			'(GMT-04:00) Atlantic Time (Canada)' => 'Canada/Atlantic',
			'(GMT-04:30) Caracas' => 'America/Caracas',
			'(GMT-04:00) La Paz' => 'America/La_Paz',
			'(GMT-04:00) Santiago' => 'America/Santiago',
			'(GMT-03:30) Newfoundland' => 'Canada/Newfoundland',
			'(GMT-03:00) Brasilia' => 'America/Sao_Paulo',
			'(GMT-03:00) Buenos Aires' => 'America/Argentina/Buenos_Aires',
			'(GMT-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',
			'(GMT-03:00) Greenland' => 'America/Godthab',
			'(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
			'(GMT-01:00) Azores' => 'Atlantic/Azores',
			'(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
			'(GMT+00:00) Casablanca' => 'Africa/Casablanca',
			'(GMT+00:00) Edinburgh' => 'Europe/London',
			'(GMT+00:00) Greenwich Mean Time : Dublin' => 'Etc/Greenwich',
			'(GMT+00:00) Lisbon' => 'Europe/Lisbon',
			'(GMT+00:00) London' => 'Europe/London',
			'(GMT+00:00) Monrovia' => 'Africa/Monrovia',
			'(GMT+00:00) UTC' => 'UTC',
			'(GMT+01:00) Amsterdam' => 'Europe/Amsterdam',
			'(GMT+01:00) Belgrade' => 'Europe/Belgrade',
			'(GMT+01:00) Berlin' => 'Europe/Berlin',
			'(GMT+01:00) Bern' => 'Europe/Berlin',
			'(GMT+01:00) Bratislava' => 'Europe/Bratislava',
			'(GMT+01:00) Brussels' => 'Europe/Brussels',
			'(GMT+01:00) Budapest' => 'Europe/Budapest',
			'(GMT+01:00) Copenhagen' => 'Europe/Copenhagen',
			'(GMT+01:00) Ljubljana' => 'Europe/Ljubljana',
			'(GMT+01:00) Madrid' => 'Europe/Madrid',
			'(GMT+01:00) Paris' => 'Europe/Paris',
			'(GMT+01:00) Prague' => 'Europe/Prague',
			'(GMT+01:00) Rome' => 'Europe/Rome',
			'(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',
			'(GMT+01:00) Skopje' => 'Europe/Skopje',
			'(GMT+01:00) Stockholm' => 'Europe/Stockholm',
			'(GMT+01:00) Vienna' => 'Europe/Vienna',
			'(GMT+01:00) Warsaw' => 'Europe/Warsaw',
			'(GMT+01:00) West Central Africa' => 'Africa/Lagos',
			'(GMT+01:00) Zagreb' => 'Europe/Zagreb',
			'(GMT+02:00) Athens' => 'Europe/Athens',
			'(GMT+02:00) Bucharest' => 'Europe/Bucharest',
			'(GMT+02:00) Cairo' => 'Africa/Cairo',
			'(GMT+02:00) Harare' => 'Africa/Harare',
			'(GMT+02:00) Helsinki' => 'Europe/Helsinki',
			'(GMT+02:00) Istanbul' => 'Europe/Istanbul',
			'(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
			'(GMT+02:00) Kyiv' => 'Europe/Helsinki',
			'(GMT+02:00) Pretoria' => 'Africa/Johannesburg',
			'(GMT+02:00) Riga' => 'Europe/Riga',
			'(GMT+02:00) Sofia' => 'Europe/Sofia',
			'(GMT+02:00) Tallinn' => 'Europe/Tallinn',
			'(GMT+02:00) Vilnius' => 'Europe/Vilnius',
			'(GMT+03:00) Baghdad' => 'Asia/Baghdad',
			'(GMT+03:00) Kuwait' => 'Asia/Kuwait',
			'(GMT+03:00) Minsk' => 'Europe/Minsk',
			'(GMT+03:00) Nairobi' => 'Africa/Nairobi',
			'(GMT+03:00) Riyadh' => 'Asia/Riyadh',
			'(GMT+03:00) Volgograd' => 'Europe/Volgograd',
			'(GMT+03:30) Tehran' => 'Asia/Tehran',
			'(GMT+04:00) Abu Dhabi' => 'Asia/Muscat',
			'(GMT+04:00) Baku' => 'Asia/Baku',
			'(GMT+04:00) Moscow' => 'Europe/Moscow',
			'(GMT+04:00) Muscat' => 'Asia/Muscat',
			'(GMT+04:00) St. Petersburg' => 'Europe/Moscow',
			'(GMT+04:00) Tbilisi' => 'Asia/Tbilisi',
			'(GMT+04:00) Yerevan' => 'Asia/Yerevan',
			'(GMT+04:30) Kabul' => 'Asia/Kabul',
			'(GMT+05:00) Islamabad' => 'Asia/Karachi',
			'(GMT+05:00) Karachi' => 'Asia/Karachi',
			'(GMT+05:00) Tashkent' => 'Asia/Tashkent',
			'(GMT+05:30) Chennai' => 'Asia/Calcutta',
			'(GMT+05:30) Kolkata' => 'Asia/Kolkata',
			'(GMT+05:30) Mumbai' => 'Asia/Calcutta',
			'(GMT+05:30) New Delhi' => 'Asia/Calcutta',
			'(GMT+05:30) Sri Jayawardenepura' => 'Asia/Calcutta',
			'(GMT+05:45) Kathmandu' => 'Asia/Katmandu',
			'(GMT+06:00) Almaty' => 'Asia/Almaty',
			'(GMT+06:00) Astana' => 'Asia/Dhaka',
			'(GMT+06:00) Dhaka' => 'Asia/Dhaka',
			'(GMT+06:00) Ekaterinburg' => 'Asia/Yekaterinburg',
			'(GMT+06:30) Rangoon' => 'Asia/Rangoon',
			'(GMT+07:00) Bangkok' => 'Asia/Bangkok',
			'(GMT+07:00) Hanoi' => 'Asia/Bangkok',
			'(GMT+07:00) Jakarta' => 'Asia/Jakarta',
			'(GMT+07:00) Novosibirsk' => 'Asia/Novosibirsk',
			'(GMT+08:00) Beijing' => 'Asia/Hong_Kong',
			'(GMT+08:00) Chongqing' => 'Asia/Chongqing',
			'(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',
			'(GMT+08:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
			'(GMT+08:00) Kuala Lumpur' => 'Asia/Kuala_Lumpur',
			'(GMT+08:00) Perth' => 'Australia/Perth',
			'(GMT+08:00) Singapore' => 'Asia/Singapore',
			'(GMT+08:00) Taipei' => 'Asia/Taipei',
			'(GMT+08:00) Ulaan Bataar' => 'Asia/Ulan_Bator',
			'(GMT+08:00) Urumqi' => 'Asia/Urumqi',
			'(GMT+09:00) Irkutsk' => 'Asia/Irkutsk',
			'(GMT+09:00) Osaka' => 'Asia/Tokyo',
			'(GMT+09:00) Sapporo' => 'Asia/Tokyo',
			'(GMT+09:00) Seoul' => 'Asia/Seoul',
			'(GMT+09:00) Tokyo' => 'Asia/Tokyo',
			'(GMT+09:30) Adelaide' => 'Australia/Adelaide',
			'(GMT+09:30) Darwin' => 'Australia/Darwin',
			'(GMT+10:00) Brisbane' => 'Australia/Brisbane',
			'(GMT+10:00) Canberra' => 'Australia/Canberra',
			'(GMT+10:00) Guam' => 'Pacific/Guam',
			'(GMT+10:00) Hobart' => 'Australia/Hobart',
			'(GMT+10:00) Melbourne' => 'Australia/Melbourne',
			'(GMT+10:00) Port Moresby' => 'Pacific/Port_Moresby',
			'(GMT+10:00) Sydney' => 'Australia/Sydney',
			'(GMT+10:00) Yakutsk' => 'Asia/Yakutsk',
			'(GMT+11:00) Vladivostok' => 'Asia/Vladivostok',
			'(GMT+12:00) Auckland' => 'Pacific/Auckland',
			'(GMT+12:00) Fiji' => 'Pacific/Fiji',
			'(GMT+12:00) International Date Line West' => 'Pacific/Kwajalein',
			'(GMT+12:00) Kamchatka' => 'Asia/Kamchatka',
			'(GMT+12:00) Magadan' => 'Asia/Magadan',
			'(GMT+12:00) Marshall Is.' => 'Pacific/Fiji',
			'(GMT+12:00) New Caledonia' => 'Asia/Magadan',
			'(GMT+12:00) Solomon Is.' => 'Asia/Magadan',
			'(GMT+12:00) Wellington' => 'Pacific/Auckland',
			'(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu'
	);
	public function init()
	{
		
		$this->getView()->headScript()->appendFile($this->getView()->baseUrl('/templates/admin/js/moment.min.js'));
		$this->getView()->headScript()->appendFile($this->getView()->baseUrl('/templates/admin/js/bootstrap-datetimepicker.js'));
		$this->getView()->headLink()->appendStylesheet($this->getView()->baseUrl("/templates/admin/css/bootstrap-datetimepicker.css"));
		$this->setMethod('post');
		$this->setAttribs(array(
				'role' => 'form',
				'class' => 'form-horizontal'
		))->setDecorators(array(
				'FormElements',
				'Form'
		))->setIsArray(true);
		
		
		// Account Detail
		
		$this->addElement('text', 'real_name', array(
				'label' => 'Real Name:',
				'required' => true,
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'username', array(
				'label' => 'Login Name:',
				'required' => true,
				'filters'    => array('StringTrim'),
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('password', 'password', array(
				'label' => 'Password:',
				'filters'    => array('StringTrim', 'StripTags'),
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('password', 'password_confirm', array(
				'label' => 'Confirm Password:',
				'filters'    => array('StringTrim', 'StripTags'),
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'email', array(
				'label' => 'Email:',
				'required' => true,
				'filters'    => array('StringTrim', 'StripTags'),
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'register_date', array(
				'label' => 'Registration Date:',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('openAddon'=>'HtmlTag'), array('tag'=>'span', 'class'=>'input-group-addon input-group-date', 'placement' => 'append', 'openOnly' => true)),
						array(array('addon'=>'HtmlTag'), array('tag'=>'span', 'class'=>'glyphicon glyphicon-calendar', 'placement' => 'append')),
						array(array('closeAddon'=>'HtmlTag'), array('tag'=>'span', 'placement' => 'append', 'closeOnly' => true)),
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'data-toggle'=>'datetimepicker', 'data-date-format'=>'DD-MM-YYYY hh:mm A', 'class'=>'input-group date')),
						array(array('warp2'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9 button-group')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'last_visit_date', array(
				'label' => 'Last Visit:',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('openAddon'=>'HtmlTag'), array('tag'=>'span', 'class'=>'input-group-addon input-group-date', 'placement' => 'append', 'openOnly' => true)),
						array(array('addon'=>'HtmlTag'), array('tag'=>'span', 'class'=>'glyphicon glyphicon-calendar', 'placement' => 'append')),
						array(array('closeAddon'=>'HtmlTag'), array('tag'=>'span', 'placement' => 'append', 'closeOnly' => true)),
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'data-toggle'=>'datetimepicker', 'data-date-format'=>'DD-MM-YYYY hh:mm A', 'class'=>'input-group date')),
						array(array('warp2'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9 button-group')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('radio', 'active', array(
				'label' => 'Receive Activation email:',
				'value' => 1,
				'multiOptions' => array(
						0 => 'No',
						1 => 'Yes'
				),
				'label_class' => 'btn btn-default btn-sm',
				'separator'   => '',
				'decorators' =>  array(  
					'ViewHelper',
	    			array(array('Value'=>'HtmlTag'), array('tag'=>'div','class'=>'col-sm-9 btn-group btn-group-yes-no', 'data-toggle' => 'buttons')),
	    			array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
					array('Label',array('class'=>'small col-sm-3 control-label')),
	    			array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));

		$this->addElement('radio', 'block', array(
				'label' => 'Enable this User:',
				'value' => 1,
				'multiOptions' => array(
						0 => 'No',
						1 => 'Yes'
				),
				'label_class' => 'btn btn-default btn-sm',
				'separator'   => '',
				'decorators' =>  array(  
					'ViewHelper',
	    			array(array('Value'=>'HtmlTag'), array('tag'=>'div','class'=>'col-sm-9 btn-group btn-group-yes-no', 'data-toggle' => 'buttons')),
	    			array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
					array('Label',array('class'=>'small col-sm-3 control-label')),
	    			array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('text', 'id', array(
				'label' => 'User ID:',
				'filters'    => array('StringTrim'),
				'class' => 'form-control input-sm',
				'attribs' => array('disabled' => true),
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addDisplayGroup(
				array('real_name', 'username', 'password', 'password_confirm', 'email', 'register_date', 'last_visit_date', 'active', 'block', 'id'), 
				'detail',
    			array(
    					'disableLoadDefaultDecorators' => true,
    					'decorators' => array(
							    'FormElements',
    							array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'form-horizontal'))
						)
    					
				)
		);
		
		// Account Settings
		$this->addElement('select', 'template', array(
				'label' => 'Template:',
				'belongsTo' => 'params',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('select', 'language', array(
				'label' => 'Language:',
				'belongsTo' => 'params',
				'class' => 'form-control input-sm',
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addElement('select', 'timezone', array(
				'label' => 'Time Zone:',
				'belongsTo' => 'params',
				'class' => 'form-control input-sm',
				'multiOptions' => array_flip ($this->timezones),
				'decorators' => array(
						'ViewHelper',
						array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'col-sm-9')),
						array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
						array('Label',array('class'=>'small col-sm-3 control-label')),
						array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
				)
		));
		
		$this->addDisplayGroup(
				array('template', 'language', 'timezone'),
				'settings',
				array(
						'disableLoadDefaultDecorators' => true,
						'decorators' => array(
								'FormElements',
								array(array('warp'=>'HtmlTag'), array('tag'=>'div', 'class'=>'form-horizontal'))
						)
							
				)
		);
		
		//Assigned Groups
		$this->addElement (
				'multiCheckbox', 'groups',
				array (
						'checkedValue' => '0',
						'required' => true,
						'separator'   => '</div><div class="checkbox">',
						'decorators' =>  array(  
							'ViewHelper',
			    			array(array('ulWrapper' => 'HtmlTag'), array('tag' => 'div', "class"=>"checkbox")),
			    			array(array('Value'=>'HtmlTag'), array('tag'=>'div')),
			    			array('Errors',array('class' => 'col-md-offset-3 clearfix', 'style' => 'margin-bottom:0px;')),
							array('Label',array('class'=>'small col-sm-3 control-label')),
			    			array(array('Group'=>'HtmlTag'), array('tag'=>'div','class'=>'form-group'))
						)
				)
		);
	}
}
?>