# BYR Excel Plugin

## What is it
It is a PHP and jQuery plugin for web developers to develope code whose purpose is to import excel `(WITHOUT ERRORS)` easier.

## Requirements
* PHP 5.5+
* jQuery 1.8.2
* PHPExcel

## Demo

First create a new BYRExcel Object and set the rules

```php
//demo.php
$excel = new BYRExcel('test.xlsx');
$rules = array(
    array('电话', 'phone'),
    array('姓名', 'checkname'),
    );

```

Then execute validate

```php
$result = $excel->validate($rules);
```

`result` is the return of the validation, if the data is correct, its value is true, else you can get the json data which need to be sent to jquery 
```php
if(!$result){
    $ret = $excel->getJSONData();
    die($ret);
}else{
  //do something to import your data
}
```

And you need to write your forwardend code to display the errors 
```html
<div id="container"></div>
<script>
$("#container").byrexcel({
    url:'./demo.php',
    submiturl:'./submit.php',
    maxrows : 100
  });
</script>
```
variable `url` means where you first get the data
variable `submiturl` means where you send your changed data to
variable `maxrows` means how many rows to display in your browser


Then you need to set a change to modify changed data
```php
//submit.php
$data = $_POST['excel_data'];
$id = $data['ID'];
$excel2 = new BYRExcel($id);
$result = $excel2->modify($data['items']);
if(!$result){
    $ret = $excel2->getJSONData();
}
else{
    $ret = json_encode(true);
}
die($ret);
```
The return value is just like what it is in the first

## Customize functions
You can customize your own validation function and use it in the rules like
```php
array('ColToValidate','YourValidationFunction')
```
And your function need to be like this
```php
function YourOwnFunction($values, $attribute)
{
    //something to be done
    if($validationTrue)
    {
        return true;
    }
    else
    {
        return "String that describe why";
    }
}
```

## To Do 
* add style on the plugin, now it is ugly
* rebuild jquery code which is ugly now 
* add export function with less memory usage
* add more rule support like xml or yaml etc
