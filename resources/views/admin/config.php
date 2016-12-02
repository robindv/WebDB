<?php

echo '<h2>Configuratie</h2>';

echo Form::open(['id'=>'modal_form','class'=>'form-horizontal']);

echo Form::b_dropdown($errors, 'cloudstack_zoneid', 'Zone', $zones, $current_zoneid, 3);
echo Form::b_dropdown($errors, 'cloudstack_networkid', 'Netwerk', $networks, $current_networkid, 6);
echo Form::b_dropdown($errors, 'cloudstack_serviceofferingid', 'Service offering', $serviceofferings, $current_serviceofferingid, 6);
echo Form::b_dropdown($errors, 'cloudstack_templateid', 'Template', $templates, $current_templateid, 6);

echo '<br />';
echo Form::submit('Opslaan',['class'=>'btn btn-default col-xs-offset-1']);

echo Form::close();
