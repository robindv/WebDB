<?php

Route::get('/{any}', 'SPAController@getIndex')->where('any', '.*');
