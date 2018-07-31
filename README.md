# Sales Order Module
php and oracle remote connect database to create order and distribution module.

# Sales Order Module
> Raw PHP based project to manage sales orders via remotly connect oracle database. 

[![NPM Version][npm-image]][npm-url]
[![Build Status][travis-image]][travis-url]
[![Downloads Stats][npm-downloads]][npm-url]

Sales Order Module is a raw php based project to manage sales order that is connected to Oracle database. It has some simple sql statement that will help to begginers who wanted to start a php project that integrated to Oracle database. Also it has some common and useful jquery code that is useful for e-commerce based project. You are welcome to contribute this project.  

![](header.png)


## Usage example

Very simple design layout, it has so CSS Frameworks(Like Bootstrap, Materialize...). E-commerce and ERP solution uses this types of client interactions. 


## Development setup

Run this command in your terminal to Clone this project:

```sh
git clone https://github.com/icelimon/orderModule.git
```

After installation, you have to create two oracle schema named, 
1. DEALER_POST_MST with the following fields..
 - DEALER_PO_NO, BRANCH_ID, DEALER_ID, SUB_DEALER_ID, DEALER_PO_DATE, PO_DELIVERY_DATE

2. DEALER_POST_DTL with the following fields..
 - PO_DLT_SL, DEALER_PO_NO, ITEM_ID, DISCOUNT_PCT, ITEM_QNTY, CREATE_EMP_ID, CREATE_DATE

## Meta

Shariful Islam – [@YourTwitter](https://twitter.com/icelimonbd) – icelimon.pro@gmail.com


[https://github.com/icelimon/orderModule](https://github.com/icelimon/)

## Contributing

1. Fork it (<https://github.com/icelimon/orderModule/fork>)
2. Create your feature branch (`git checkout -b feature/fooBar`)
3. Commit your changes (`git commit -am 'Add some fooBar'`)
4. Push to the branch (`git push origin feature/fooBar`)
5. Create a new Pull Request

<!-- Markdown link & img dfn's -->
[npm-image]: https://img.shields.io/npm/v/datadog-metrics.svg?style=flat-square
[npm-url]: https://npmjs.org/package/datadog-metrics
[npm-downloads]: https://img.shields.io/npm/dm/datadog-metrics.svg?style=flat-square
[travis-image]: https://img.shields.io/travis/dbader/node-datadog-metrics/master.svg?style=flat-square
[travis-url]: https://travis-ci.org/dbader/node-datadog-metrics
