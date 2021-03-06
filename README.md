# Pepper

Pepper will analyze your code and find different bad practices like methods
that are too long or using the double equals.

Pepper is good for you.

## Installation

With composer for a local lala.

With PEAR:
   pear install pepper

## Usage

Run pepper without any parameters to see the usage.

To analyze a single file:
```bash
pepper -f <file>
```

## Configuration

By default, pepper will search for the `pepperconfig.yml` file in the current directory. If none is found it will run with some default configuration.
The `pepperconfig.yml` sould be as follows:

```yaml
RuleName1:
  level: [notice|warning|error]
  params:
      param1: value1
      param2: value2

RuleName2:
  level: [notice|warning|error]
  params:
      param1: value1
      param2: value2
```

The `params` key is optional and is used only if the rule can be configured. For exemple, the rule for global variables doesn't need any configuration,
on the other hand, the rule for checking method length takes a parameter named `threshold`.

Here is a real example of a pepper configuration file :
```yaml
Pepper\Rule\MethodTooLong:
   level: warning
   params:
      threshold: 2

Pepper\Rule\UnnecessaryIf:
   level: warning

Pepper\Rule\GlobalVariable:
   level: notice
```

## Rules

Pepper has a growing number of rules that you can activate at your will:

### Double equals

Will warn you about the usage of the double equals statement.

PHP has two comparison operators, the equals operator (==) and the identity operator (===).
It is considered bad practice to use the double equals operator.

Some examples why using the double equals operator may introduce bugs in your code:
```php
1 == '1' // true, note that this can be useful in some cases
'1 some text' == '1' // false
'1 some text' == 1 // true
```
Also, note that
```php
'424572979023470974209347293457249724907234928347' == $n;
```
is way slower than
```php
$n == '424572979023470974209347293457249724907234928347';
```
I will explain this in a second.

On the other hand, here is what would the identity operator return:
```php
1 === '1' // false
'1 some text' === '1' // false
'1 some text' === 1 // false
```
Here is an overview of the _double equals_ algorithm:
Let _a_ and _b_ be the left and right values.
 * Compare the types of the two values
 * If the two values have the same type, compare their values.
 * If the two values don't have the same type:
    * If _a_ is a string and _b_ is a number, convert _a_ to a number and then compare the two values.
    * yada yada yada


All that being said, you should not follow this kind of "best practices" blindly. It is important that you understand what is happening behind the scene in order to use the appropriate operators. This is exactly why **Pepper** can be configured not to show warnings line by line in your code.

### Global variable

Will warn you about the usage of global variable.

### Method too long

Will warn you if a method is too long. When is a method too long ? That's up
to you to decide, everything is customizable in Pepper.

### Nesting too deep

Too much nesting is not good for you, Pepper can warn you if you have to much
nesting in your code. Once again, customizable, Pepper is awesome!

## License

[MIT](http://rumpl.mit-license.org)
