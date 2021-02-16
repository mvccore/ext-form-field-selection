# MvcCore - Extension - Form - Field - Selection

[![Latest Stable Version](https://img.shields.io/badge/Stable-v5.1.4-brightgreen.svg?style=plastic)](https://github.com/mvccore/ext-form-field-selection/releases)
[![License](https://img.shields.io/badge/License-BSD%203-brightgreen.svg?style=plastic)](https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md)
![PHP Version](https://img.shields.io/badge/PHP->=5.4-brightgreen.svg?style=plastic)

MvcCore form extension with fields select, country select, checkbox(es), radios and color.

## Installation
```shell
composer require mvccore/ext-form-field-selection
```

## Fields And Default Validators
- `select`, country `select`
	- `ValueInOptions`
		- **configured by default**
		-  validate if submitted string(s) are presented in select options keys.
- `input:checkbox`
	- `SafeString`
		- **configured by default**
		- XSS string protection to safely display submitted value in response, configured by default
- `input:radio` - radio group and `input:checkbox`es - checkbox group
	- `ValueInOptions` - **configured by default**, ...description above
- `input:color`
	- `Color`
		- **configured by default**
		- validate hexadecimal color with no transparency including leading hash char `#`

## Features
- always server side checked attributes `required`, `disabled` and `readonly`
- all HTML5 specific and global atributes (by [Mozilla Development Network Docs](https://developer.mozilla.org/en-US/docs/Web/HTML/Reference))
- every field has it's build-in specific validator described above
- every build-in validator adds form error (when necessary) into session
  and than all errors are displayed/rendered and cleared from session on error page, 
  where user is redirected after submit
- any field is possible to render naturally or with custom template for specific field class/instance
- very extensible field classes - every field has public template methods:
	- `SetForm()`		- called immediatelly after field instance is added into form instance
	- `PreDispatch()`	- called immediatelly before any field instance rendering type
	- `Render()`		- called on every instance in form instance rendering process
		- submethods: `RenderNaturally()`, `RenderTemplate()`, `RenderControl()`, `RenderLabel()` ...
	- `Submit()`		- called on every instance when form is submitted

## Examples
- [**Application - Questionnaires (mvccore/app-questionnaires)**](https://github.com/mvccore/app-questionnaires)

## Basic Example

```php
$form = (new \MvcCore\Ext\Form($controller))->SetId('job_hunting');
...
$jobQual = new \MvcCore\Ext\Forms\Fields\Select;
$jobQual
	->SetName('job_qualification')
	->SetLabel('Job Qualificatio:')
	->SetOptions([
		'junior'	=> 'Junior Developer',
		'senior'	=> 'Senior developer',
		'manager'	=> 'IT Manager',
	]);
$gender = new \MvcCore\Ext\Forms\Fields\Radio([
	'name'		=> 'gender',
	'label'		=> 'Gender:',
	'options'	=> [
		'M'			=> 'Male',
		'F'			=> 'Female',
		'O'			=> 'Other',
	],
]);
$country = new \MvcCore\Ext\Forms\Fields\CountrySelect([
	'name'		=> 'country',
	'label'		=> 'Country:',
	'filter'	=> ['DE', 'AT', 'FR', 'NL'],
]);
$skills = new \MvcCore\Ext\Forms\Fields\CheckboxGroup([
	'name'		=> 'skills',
	'label'		=> 'I control programming languages:',
	'options'	=> [
		'html'		=> 'HTML5',
		'css'		=> 'CSS3',
		'js'		=> 'Javascript',
		'ts'		=> 'Typescript',
		'php'		=> 'PHP',
		'cs'		=> 'C#',
		'vb'		=> 'Visual Basic',
		'java'		=> 'Java',
		'py'		=> 'Python',
		'pl'		=> 'Perl',
	],
]);
$public = new \MvcCore\Ext\Forms\Fields\Checkbox([
	'name'		=> 'public',
	'label'		=> 'Yes, anybody could see my profile.',
	'checked'	=> TRUE,
]);
$color = new \MvcCore\Ext\Forms\Fields\Color([
	'name'		=> 'profile_color',
	'label'		=> 'My profile color:',
	'value'		=> '#0000FF',
]);

...
$form->AddFields($jobQual, $gender, $country, $skills, $public, $color);
```
