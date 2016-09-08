<?php

putenv("username=example@example.com");
putenv("password=passwordgoeshere");
putenv("testToken=keygoeshere");

return array(

  'leadpagesUsername' => getenv('username'),
  'leadpagesPassword' => getenv('password'),
  'adminUsername'     => getenv('adminUsername'),
  'adminPassword'     => getenv('adminPassword'),

);