Yii2 workbench
=============
[![Latest Stable Version](https://poser.pugx.org/johnitvn/yii2-workbench/v/stable)](https://packagist.org/packages/johnitvn/yii2-workbench)
[![License](https://poser.pugx.org/johnitvn/yii2-workbench/license)](https://packagist.org/packages/johnitvn/yii2-workbench)
[![Total Downloads](https://poser.pugx.org/johnitvn/yii2-workbench/downloads)](https://packagist.org/packages/johnitvn/yii2-workbench)
[![Monthly Downloads](https://poser.pugx.org/johnitvn/yii2-workbench/d/monthly)](https://packagist.org/packages/johnitvn/yii2-workbench)
[![Daily Downloads](https://poser.pugx.org/johnitvn/yii2-workbench/d/daily)](https://packagist.org/packages/johnitvn/yii2-workbench)

Yii2 workbench support for yii2 extension development

When you development package yii2 the default you have 2 options:
+ You can store source code in github or local git repository
+ You can store source code in local disk and add extensions in to extension config of yii

The weakness of above options:
+ With the first option, you take a lot time with `composer update`. 
Maybe peoples in US Coast (location places of Packagist and Github server) is better luck other ones because time to run `\composer update` will be shorter than peoples in other regions.<BR>
Of course, you can pull and push directly in vendor directory for you package.
But when you work with multiple packages paralel.
When you want to add required a new dependency package. 
You must push all your packages to server and wait to `composer update`. 
Really bad, sometime i have take 30 minutes for `composer update`.

+ With the second option, you must define required package in composer.json of main project,
 and define the bootstrap class in web config. The manipulation looks unnatural like when you release or public your package.

That's all reason for development this package. I have been work with laravel. They have feature called workbench, too. 
I really like the way workbench work. And now, after i'm developed workbench, just change code and refresh browser. 
Very amazing for my work, and you how do feel about this extension let me know your comment.

Features
------------
+ Support for quick development extension in Yii2
+ Support autoload with package's composer.json
+ Support bootstrap in composer.json

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist johnitvn/yii2-workbench "*"
```

or add

```
"johnitvn/yii2-workbench": "*"
```

to the require section of your `composer.json` file.


Configuration
-----
Let add workbench component in both your main config and console config

````
'components' => [
    'workbench'=>[
        'class' => 'johnitvn\workbench\Workbench',
        'workingDir' => dirname(__DIR__).'/workbench',
        'author' => "Author Name",
        'email' => "author@example.com",
        'onlyIncludePackages' => [
            'johnitvn/yii2-ajaxcrud',
        ]
    ],
]
````

The working dir is directory you want to develop package.<br>
The author and email use for create package template.<BR>
The onlyIncludePackages is list package you want to load. Set null if you want to load all


Usage
-----
The first let create your new package.

Use this command

````bash
$ yii workbench/create {vendor}/{package}
````

or 

````bash
$ yii workbench/create
````

If you don't set package fullname param. Application will prompt you.

Now you can see in the {workingDir}/{vendor}/{package}. The template of package is

````
- src/
- test/
- vendor/
- phpunit.xml
- composer.json
````

File composer.json defined the autoload psr-4 for you.You can change it if you want<BR>
If you want to define bootstrap just add to composer.json
````json
"extra": {
    "bootstrap": "{vendor}\\{package}\\Bootstrap"
}
````
For psr-4 and bootstrap change you just run application don't need update anything. When you add new dependency into composer.json you must run `composer update` in your package root.

One more thing, when you add dependency to one package exist in workbench workspace, workbench with not load from package's vendor, workbench will load from workspace.



