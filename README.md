# Billing Boss 

[![Build Status](https://travis-ci.org/ranskills/billing-boss-php.svg?branch=master)](https://travis-ci.org/ranskills/billing-boss-php)
[![codecov](https://codecov.io/gh/ranskills/billing-boss-php/branch/master/graph/badge.svg)](https://codecov.io/gh/ranskills/billing-boss-php)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

**Billing Boss** is a library that implements billing using a domain-specific language (DSL) to express a billing structure to be applied.

The library has implementations in the following languages:
- [Java](https://github.com/ranskills/billing-boss-java "Project's Homepage")
- [PHP](https://github.com/ranskills/billing-boss-php)


## Why Use This Library
- It is a fully-tested library
- Lets you focus on developing your next great application without having to implement if, loops, etc. to apply *billing* or *discount*
- Can be used anywhere an amount is based on another amount such as billing, discounts, etc.
- Because of its use of a DSL, the billing structure can be persisted and loaded on demand allowing for different billing
definitions per customer or group of customer for example

## Billing Types Supported
- [Flat Rate](#Flat-Rate)
- [Percentage](#Percentage)
- [Capped](#Capped)
- [Segmented](#Segmented)
- [Progressive](#Progressive)

### Flat Rate
---
Flat rate, aka fixed rate, billing

> Structure: `number`

> Example: 10.0

### Percentage
--------------

> Structure: `number%`

> Example: 10.0%

### Capped
--------------

> Structure: `number%[min, max]`

> Example: 1.0%[10, 50]

### Segmented
--------------

> Structure: `number%[min - max]`

> Example: 1.0%[10, 50]


### Progressive
--------------

> Structure: `percent%, amount ( > percent%, amount)+`

> Example: `10%, 10000 > 20%, 10000`



Usage
```java
    BillContext ctxt = new BillContext(1000, "2.5%");
    double bill = BillInterpreterAggregator::interpret(ctxt);
```

|Amount| Billing Structure | Resulting Bill| Comment |
|-----:|-------------------|--------------:|---|
|1,000  |          **`5`** | 5||
|1,000  | **`1.5%`**       |15||
|1,500  | **`1%[10, 50]`** |15| 15 used because it falls in range|
|800    | **`1%[10, 50]`** |10|lower limit used|
|7,000  | **`1%[10, 50]`** |50|upper limit used|
|20,000  | **`10%, 10000 > 20%, 10000`** |200||
|30,000  | **`10%, 10000 > 20%, 10000`** |200||




## Extending The Library
Custom bill interpreters can be added to extend the capability of the library, provided your unique scenario cannot be handled by the library.

### Steps / Guide
Lets implement a bill for a hypothetical scenario where a bill is the squared values of the amount to be bills.

`bill = amount * amount`

The important decision to be made is which notation to use to represent this type of bill. The notation should be indicative of the nature of bill, so lets use `^2`

> Note: Each notation MUST be unique to ensure that it can only be interpreter by only a SINGLE interpreter


1. Implement the `BillInterpreter` interface or extend the `AbstractBillInterpreter`

    ```java
    public class SquareBillInterpreter extends AbstractBillInterpreter {

        public SquareBillInterpreter() {
            // Defining the regular expression to match the notation ^2
            super("^\\^2$")
        }

        @Override
        public double interpret(BillContext context) {
            if (!isValid(context)) return 0;

            return context.getAmount() * context.getAmount();
        }
    }
    ```
2. Register your new custom interpreter by adding it to `BillingBoss`
    ```java
    BillingBoss::addInterpreter(new SquareBillInterpreter())
    ```
That is it, you are done.

3. Testing 
    ```java
    BillContext ctxt = new BillContext(50, "^2");
    double bill = BillingBoss::bill(ctxt);
    // bill will be 2500

    ctxt.setAmount(10);
    bill = BillingBoss::bill(ctxt);
    // bill will be 100
    ```