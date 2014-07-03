yii2-basicauth
==============

Simple basic auth component.


## Required
* session component

## Howto 
1. Save BasicAuth.php to app/components directory.
2. Configure at config/params.php
3. Attached in target controller's behaviors() func.


### In controller

~~~
class AdminController extends Controller
{
	public function behaviors()
	{
		return [
			'basicAuth' => [
				'class' => "app\components\BasicAuth"
			],
		];
	}
}
~~~

### In params.php

~~~
return [
    'basicAuth'  => [
		'username' => 'password',
	],
];
~~~
