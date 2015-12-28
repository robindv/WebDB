<?php
echo Form::open(['id'=>"modal_form",'class'=>'form-horizontal']);

echo 'Weet je zeker dat je het domein <b>http';

if($domain->ssl)
    echo 's';

echo '://'.$domain->domain.'</b> wilt van server <b>'.$domain->server->name.'</b> verwijderen?';

echo Form::close();