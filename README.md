# Billing Boss 

[![Build Status](https://travis-ci.org/ranskills/billing-boss-php.svg?branch=master)](https://travis-ci.org/ranskills/billing-boss-php)
[![codecov](https://codecov.io/gh/ranskills/billing-boss-php/branch/master/graph/badge.svg)](https://codecov.io/gh/ranskills/billing-boss-php)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ranskills/billing-boss-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ranskills/billing-boss-php/?branch=master)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

**Billing Boss** is a free, open-source library that implements billing using a domain-specific language (DSL) to express a billing structure to be applied.

The library has implementations in the following languages:
- [Java](https://github.com/ranskills/billing-boss-java "Project's Homepage") [Coming soon]
- [PHP](https://github.com/ranskills/billing-boss-php)

## Features
- Intuitive notations for expressing billing/discount structures
- Common billing interpreters provided, namely [Flat Rate](#Flat-Rate), [Percentage](#Percentage), [Capped](#Capped), [Progressive](#Progressive), and [Stepped](#Stepped) interpreters
- [Highly extensible](#Extending-The-Library)
- Lightweight, just under 85KB, with no dependencies


## Why Use This Library
- It is a fully-tested library
- Lets you focus on developing your next great application without having to implement if, loops, etc. to apply *billing*, *discount*, etc.
- Can be used anywhere an amount is based on another amount such as billing, discounts, etc.
- Because of its use of a DSL, the billing structure can be persisted and loaded on demand allowing for different billing definitions to be stored with the associated entities, such as a customer, group of customers, etc.

## Installation
The recommended installation is via [composer](https://getcomposer.org) by running the command:

    $ composer require ranskills/billing-boss

OR

[download][download] the most recent archive from the releases page of the project.

## Testing
Run tests with:

    $ composer test
Make sure the test dependencies are installed by running `composer install`, if required

## Usage

```php
<?php
// 1. Import the required classes from the library
use BillingBoss\BillingBoss;
use BillingBoss\BillContext;

// 2. Define the context to be interpreted
$ctxt = new BillContext(1000, '2.5%, 1 - *');

// 3. Pass the context to be interpreted
$bill = BillingBoss::interpret($ctxt);
// $bill = 25.00
```

## Notations
Explanation of common notations used throughout the library.

|Notation Symbol| Notation Name | Note|
|---------------|---------------|-----|
| __`|`__ (pipe) | Segment | For segmented structures, i.e. those applying to different ranges, the interpreter determines which segment the billable amount falls in before the bill is applied |
| __`-`__ (hyphen) | Range |All range specifications are inclusive. E.g., 1 - 1000 means the value should be at least 1 and at most 1,000. <br>The mathematical equivalent is ```1 <= x <= 1000```. <br>The last range's upper boundary should be `*`|
| __`*`__ (asterisk) | Unspecified Amount | In a range specification, `min - max`, this can only be used in the upper boundary, `max`|


## Billing Types/Interpreters Supported
1. [Flat Rate](#Flat-Rate)
2. [Percentage](#Percentage)
3. [Capped](#Capped)
4. [Progressive](#Progressive)
5. [Stepped](#Stepped)

### Flat Rate
---
Flat rate, aka fixed rate, billing

The same amount is applied for all billable amounts within the same range.

> Structure: __`number, range (| number, range)*`__

> __Example 1: `0.50, 1 - *`__
>
> _Reads_: A flat rate of 0.50 is charged for all amounts 1 or above
>
> | Amount | Bill |
> |-------:|-----:|
> | 1      | **1.00** |
> | 5,000  | **1.00** |

> **Example 2: `1, 1 - 499.99 | 10, 500 - *`**
>
> A charge of 1 applies for amounts below 500 to 1, otherwise a charge of 10 applies to amounts from 500 upwards
>
> | Amount | Bill |
> |-------:|-----:|
> | 1      | **1.00** |
> | 5,000  | **10.00**|


### Percentage
--------------
The bill is a percentage of the billable amount

> Structure: __`number%, range (| number%, range)*`__

> _Example 1_: __1%, 1 - *__
>
> _Reads_: For amounts greater than or equal to 1, charge 1%
>
> | Amount | Bill |
> |-------:|-----:|
> | 1      | **0.01** |
> | 5,000   | __50.00__|

> Example 2: __1%, 1 - 500 | 3%, 501 - 2000 | 5%, 2001 - *__
>
> _Reads_:
>
> - Charge 1% for amounts between 1 and 500
> - Charge 3% for amounts between 501 to 2,000
> - Charge 5% for amounts

> | Amount | Bill |
> |-------:|-----:|
> | 1      | **0.01** |
> | 5,000   | **250.00**|


### Capped
--------------
The bill is expressed as a percentage of the billable amount but it is constrained or capped, unlike the [percentage billing](#Percentage), within a specified boundary that the bill can not fall outside of.

> Structure: __`number% [min, max], range_start - range_end`__

> Example 1: __1% [5, 100], 1 - *__
>
> _Reads_:
For amounts of at least 1: <br>
if the charge is below the minimum, take the minimum (5)<br>
if the charge is above the maximum, take the maximum (100)<br>
otherwise the charge applies
> | Amount  | Bill |
> |--------:|-----:|
> | 10      | **5.00**  |
> | 100     | **5.00**  |
> | 5,000   | **50.00** |
> | 10,000  | **100.00**|
> | 100,000 | **100.00**|

> Example 2: __1% [5, 100], 1 - 20000 | 2% [500, 1500], 20001 - *__
>
> | Amount      | Bill          |
> |-----------: |--------------:|
> | 5,000       | **50.00**     |
> | 10,000      | **100.00**    |
> | 20,000      | **100.00**    |
> | 20,001      | **500.00**    |
> | 50,000      | **1,000.00**  |
> | 200,000     | **1,500.00**  |
> | 1,000,000   | **1,500.00**  |

For an amount between 1 to 1,000, charge 1% which is capped between 5 to 100. All amounts at least 1,001 should be charged at 2% capped between 10 and 200.


### Progressive
--------------
Progressively/iteratively applies the billing structure until the remaining amount to bill is exhausted

> Structure: __`percent%, amount ( > percent%, amount)+`__

> Example: __`0%, 261 > 5%, 70 > 10%, 100 > 17.5%, 2810 > 25%, *
`__

The example represents the structure for Ghana's income tax.

- The first GHS **261.00** earned attracts **no tax**
- Up to the next GHS **70.00** attracts **5%**
- Up to the next GHS **100.00** attracts **10%**
- Up to the next GHS **2,810.00** attracts **17.5%**
- **25%** applies to the remaining amount

### Stepped
---
Every step the amount graduates to attracts the fixed charge. The amount billed is the accumulation of these charges

> Structure: __`charge, amount+`__

> Example: __`1, 100+`__



## Extending The Library
Custom interpreters can be added to extend the capability of the library, provided your unique scenario cannot be handled by the library through the defined interpreters.

### Steps

1. Either ***implement*** the **BillInterpreter** interface or ***extend*** the **AbstractBillInterpreter** abstract class that do provide some convenient methods

2. Define a unique structure for the bill, making use of the notations discussed in [notations](#Notations) above, if need be

#### Sample Implementation Guide
Lets implement a bill for a hypothetical scenario where a bill is the squared value of the amount to be billed.

    bill = amount * amount

The important decision to be made is the notation to use to represent this type of bill. The notation should be indicative of the nature of bill, so lets use **`^2`**

> Note: Each notation **MUST** be unique to ensure that it can be interpreted by only a SINGLE interpreter


1. Implement the `BillInterpreter` interface or extend the `AbstractBillInterpreter` in the file _SquareBillInterpreter.php_

    ```php
    <?php

    namespace YourProject\BillingBoss\Interpreter;

    use BillingBoss\AbstractBillInterpreter;

    class SquareBillInterpreter extends AbstractBillInterpreter {

        public function __construct() {
            // Defining the regular expression to match the notation ^2
            parent::__construct('/^\^2$/');
        }

        public function interpret(BillContext context) {
            if (!isValid(context)) return 0.0;

            return context.getAmount() * context.getAmount();
        }
    }
    ```

2. Register your new custom interpreter by adding it to `BillingBoss`
    ```php
    BillingBoss::addInterpreter(new SquareBillInterpreter())
    ```
That is it, you are done.

3. Testing 
    ```php
    $ctxt = new BillContext(50, "^2");
    double bill = BillingBoss::bill($ctxt);
    // bill will be 2500

    $ctxt.setAmount(10);
    bill = BillingBoss::bill($ctxt);
    // bill will be 100
    ```


## Report
---
Report issues, feature requests, etc. [here][issues]

[issues]: https://github.com/ranskills/billing-boss-php/issues
[download]: https://github.com/ranskills/billing-boss-php/releases "Download archive"