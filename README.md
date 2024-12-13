# OpenCart Products module pack \[PMP\]
[![License: GPLv3](https://img.shields.io/badge/license-GPL%20V3-green?style=plastic)](LICENSE)

PMP creates modules for displaying products and uses different data sources for them (latest products, special, bestsellers, etc.). In addition to the global mode (which is implemented in the standard modules of the engine), absolute and relative modes are implemented in PMP.

For example, "Latest from the selected category/manufacturer", "Latest from the current category/manufacturer", "Latest from the same category/manufacturer as the current product". Details below.

## Other Languages

* [Russian](README_RU.md)

## Change Log

* [CHANGELOG.md](docs/CHANGELOG.md)

## Screenshots

* [SCREENSHOTS.md](docs/SCREENSHOTS.md)

## Features

### Operating modes

Data sources (latest products, special, bestsellers, etc.) can work in three modes. Let me explain using the example of the "Latest" module:

#### Absolute and Global modes

* Latest products from manually selected categories / manufacturers. This is **Absolute mode**.
* If you don't select any category/manufacturer, it will be **Global mode**, like the standard latest products module.

Depends on manually specified parameters. You can specify both the category and the manufacturer at the same time.

#### Relative Mode

* Latest products of the current category / manufacturer (where the module is located / visited by the user). This mode also works on the product page, it can take into account both the category and the manufacturer of the product at the same time.
* If the module is placed on the main page or any other place where it has nothing to cling to (product, category, manufacturer), then the module will work also in **Global mode**.

Depends on the location of the module. If you place the same module of latest products at the same time on the page of categories, manufacturer and product, then it will show different sets of products on each page. An example is in the demo.

### Data sources

**Absolute, Relative, Global:**

* Latest;
* Bestseller;
* Products with discounts;
* Special;
* Products with bonus points;
* Random products;
* Most viewed products;
* Most discussed products.

**Custom:**

* Your SQL query;
* Your PHP code.

**Global:**

* Hand-picked items.

### Other features

* It is possible to invert the selection for absolute/relative data sources. This means that products NOT included in the current selection will be selected.
* There is a choice of product status, quantity, sorting, mixing of goods, and caching.
* The module supports template compatibility mode. This means that in 99% of cases, you shouldn't worry about adapting the module to the design template. This mechanism works by replacing products in the standard "Recommended Products" module. Uses the event mechanism, works without injection into files.
* The module also implements the ability to replace the template (tpl / twig) with your own. This will allow you to display products in any desired form, be it a slider / swiper, etc. This feature also works in compatibility mode.

In total, we have (16 (absolute + relative) * 2 (sample inversion)) + 8 (Global) + 3 (Additional data sources) = 43 product sampling options. You can also multiply the result by the number of your options for settings.

## Compatibility

* OpenCart 2.3, 3.x, 4.x.
* Version for 4.x is alpha. Works perfect, but has usability issues. 

## Demo

Admin

* [https://pmp.shtt.blog/admin/](https://pmp.shtt.blog/admin/) (auto login)

Catalog

* [https://pmp.shtt.blog/](https://pmp.shtt.blog/)

The demo site has a top menu for quick navigation.

## Description of the demo site

There are 3 modules created on the demo site:

#### Most Viewed - Absolute | Most viewed - Absolute

* Selected manufacturer Apple. Posted on the main page only.

#### New - Relative | Latest - Relative

* Placed on the pages of categories, manufacturers, products, and the main page. There is nothing to cling to on the main module (product, category, manufacturer), it selects new items from the entire store (Global mode).

#### Custom PHP code | custom php code

* Placed on the products page, it always displays the same products (3 products).

## Installation

* Install the extension through the standard extension installation section.
* Go to the modules section and install the "[PMP] Products module pack" module.

## Management

The module is simple, no manual is required, everything is in the description and hints.

If you have any questions, write to the support thread or send a private message.

## License

* [GPL v3.0](LICENSE.MD)

## Thank You for Using My Extensions!

I have decided to make all my OpenCart extensions free and open-source to benefit the community. Developing, maintaining, and updating these extensions takes time and effort.

If my extensions have been helpful for your project and you’d like to support my work, any donation is greatly appreciated.

### 💙 You can support me via:

* [PayPal](https://paypal.me/TalgatShashakhmetov?country.x=US&locale.x=en_US)
* [CashApp](https://cash.app/$TalgatShashakhmetov)

Your support inspires me to keep improving and developing these tools. Thank you!
