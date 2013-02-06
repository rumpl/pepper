# Pepper

Pepper will analyze your code and find different bad practices like methods
that are too long or using the double equals.

Pepper is good for you.

## Rules

Pepper has a growing number of rules that you can activate at your will:

### Double equals

Will warn you about the usage of the double equals statement.

PHP has two comparison operators, the equals operator (==) and the identity operator (===). It is considered bad practice to use the double equals operator.

Some examples why using the double equals operator may introduce bugs in your code: 
    1 == '1' // true, note that this can be useful in some cases
    '1 some text' == '1' // false
    '1 some text' == 1 // true

Also, note that 
    '424572979023470974209347293457249724907234928347' == $n;
is way slower than
    $n == '424572979023470974209347293457249724907234928347';

I will explain this in a second.

On the other hand, here is what would the identity operator return:
    1 === '1' // false
    '1 some text' === '1' // false
    '1 some text' === 1 // false

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
