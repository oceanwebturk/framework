<?php 

return [
	'csrf_input_name' => '_token',
	'honeypot' => [
		'input_name' => '_pot',
		'label' => 'Fill This Field',
		'textarea' => '<label for="{honeypot_name}_id">{honeypot_label}</label><textarea id="{honeypot_name}_id" name="{honeypot_name}"></textarea>',
		'template' => '<div style="display:none;">{textarea}</div>'
	]
];