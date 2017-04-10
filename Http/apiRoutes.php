<?php

$router->get('diagnoses/clinic', ['uses' => 'ApiController@GetDiagnoses', 'as' => 'diagnoses.clinic']);
$router->get('diagnoses/clinician', ['uses' => 'ApiController@GetDiagnoses', 'as' => 'diagnoses.clinician']);

